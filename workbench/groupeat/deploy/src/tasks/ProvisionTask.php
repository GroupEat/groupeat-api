<?php namespace Groupeat\Deploy\Tasks;

use App;
use Config;
use File;
use Rocketeer\Abstracts\AbstractTask;
use SSH;

class ProvisionTask extends AbstractTask {

    const ENV_FILE_TEMP_PATH = '/tmp/.env.production.php';

    protected $description = "Initialize the production server";

    /**
     * @var mixed
     */
    private $secrets;


    public function execute()
    {
        $githubApi = $this->makeGitHubApi();

        $sslKey = $this->getSecretFromProductionEnvFile(
            'SSL_PRIVATE_KEY',
            'Enter the SSL key: '
        );

        $sslCertificate = $this->getSecretFromProductionEnvFile(
            'SSL_CERTIFICATE',
            'Enter the SSL certificate: '
        );

        $appKey = $this->askForAppKey('Choose a 32 character string for the application key: ');

        $productionEnvVariables = $this->askProductionEnvVariables([
            'PGSQL_PASSWORD' => 'Choose the password for the postgreSQL DB: ',
            'DEFAULT_ADMIN_EMAIL' => 'Choose the default admin account email: ',
            'DEFAULT_ADMIN_PASSWORD' => 'Choose the default admin account password: ',
            'GANDI_MAIL_PASSWORD' => 'Enter the password of the Gandi Mail account: ',
        ]);

        $productionEnvVariables['APP_KEY'] = $appKey;

        $serverName = $this->getServerName();
        $githubToken = $githubApi->addOAuthToken($serverName);

        $this->addVagrantUserAndShippableKey($githubApi->getEmail(), Config::get('remote.shippable_key'));
        $githubApi->addSSHkey($this->getServerPublicKey(), $serverName);
        $this->provisionServer(Config::get('remote.domain'), $productionEnvVariables, $githubToken);
        $this->setupSSL($sslKey, $sslCertificate);
    }

    /**
     * @return \Groupeat\Deploy\Services\GitHubApi
     */
    private function makeGitHubApi()
    {
        $username = ucfirst($this->getGitHubUsername());

        $password = $this->getSecretFromProductionEnvFile(
            'GITHUB_PASSWORD',
            "$username, enter your GitHub password: "
        );

        $output = $this->explainer;
        $onError = function() { exit; };

        return App::make('GitHubApi', compact('username', 'password', 'output', 'onError'));
    }

    private function addVagrantUserAndShippableKey($email, $shippableKey)
    {
        $this->runAsRoot([
            'echo "Installing Git"',
            'apt-get install -y git',
            'echo "Adding the vagrant user"',
            'adduser --disabled-password --gecos ""  vagrant',
            'echo "Adding vagrant to the admin group"',
            'echo %vagrant ALL=NOPASSWD:ALL > /etc/sudoers.d/vagrant',
            'chmod 0440 /etc/sudoers.d/vagrant',
            'usermod -a -G sudo vagrant',
            'echo "Copying authorized keys from root to vagrant"',
            'mkdir ~vagrant/.ssh',
            'cp /root/.ssh/authorized_keys ~vagrant/.ssh/authorized_keys',
            'chown -R vagrant: ~vagrant/.ssh',
            'echo "Creating RSA keys"',
            'sudo -u vagrant echo -e  "n\n" | ssh-keygen -t rsa -N "" -C "'.$email.'" -f ~vagrant/.ssh/id_rsa',
            'echo "Adding Shippable deployment key to the authorized keys"',
            'sudo -u vagrant echo "'.$shippableKey.'" >> ~vagrant/.ssh/authorized_keys',
            'chown -R vagrant: ~vagrant/.ssh',
        ]);
    }

    private function provisionServer($domain, $productionEnVariables, $githubOAuthToken)
    {
        $envFile = $this->getProductionEnvFileContent($productionEnVariables);

        $this->explainer->line('Creating the .env.production.php file');
        $this->putFile(static::ENV_FILE_TEMP_PATH, $envFile);

        $this->runAsRoot([
            'echo "Adding GitHub to the known hosts"',
            'sudo -u vagrant echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> ~vagrant/.ssh/config',
        ]);

        $environment = 'production';
        $this->executeScriptRemotely('provision', 'Starting provisioning', [
            $environment,
            $domain,
            $productionEnVariables['PGSQL_PASSWORD'],
        ]);

        $this->runAsRoot([
            'echo "Adding GitHub token to Composer config"',
            'composer config -g github-oauth.github.com '.$githubOAuthToken,
        ]);
    }

    private function setupSSL($privateKey, $certificate)
    {
        $this->runAsRoot([
            'echo "Writing SSL credentials into /etc/nginx/ssl"',
            'mkdir /etc/nginx/ssl',
            'echo "'.$privateKey.'" >> /etc/nginx/ssl/nginx.key',
            'echo "'.$certificate.'" >> /etc/nginx/ssl/nginx.crt',
            'service nginx restart',
        ]);
    }

    private function askForAppKey($question)
    {
        do
        {
            $key = $this->getSecretFromProductionEnvFile('APP_KEY', $question);
        } while (strlen($key) != '32');

        return $key;
    }

    private function getGitHubUsername()
    {
        $output = process('ssh -T git@github.com')->getErrorOutput();
        preg_match("/\bhi (\w+)!/i", $output, $matches);

        if (!empty($matches[1]))
        {
            $this->explainer->line("Using $matches[1] username for GitHub");

            return $matches[1];
        }
        else
        {
            return $this->command->ask('Enter your GitHub username: ');
        }
    }

    private function getServerPublicKey()
    {
        return $this->runAsRoot('cat ~vagrant/.ssh/id_rsa.pub');
    }

    private function getServerName()
    {
        return trim($this->runAsRoot('hostname', 'Fetching remote server name'));
    }

    private function getProductionEnvFileContent($productionEnVariables)
    {
        $content = "<?php\n\nreturn [\n";

        foreach ($productionEnVariables as $key => $value)
        {
            $content .= "    '$key' => '$value',\n";
        }

        $content .= "];\n";

        return $content;
    }

    private function askProductionEnvVariables(array $prompts)
    {
        $secrets = [];

        foreach ($prompts as $key => $prompt)
        {
            $secrets[$key] = $this->getSecretFromProductionEnvFile($key, $prompt);
        }

        return $secrets;
    }

    private function executeScriptRemotely($scriptName, $description, $arguments = [])
    {
        $localPath = base_path("scripts/$scriptName.sh");

        if (!file_exists($localPath))
        {
            $this->explainer->error("File $localPath do not exists.");
            exit;
        }

        $remotePath = "/tmp/$scriptName.sh";
        $this->putFile($remotePath, file_get_contents($localPath));
        $command = $remotePath.' '.implode(' ', $arguments);

        return $this->runAsRoot([
            "echo \"$description\"",
            "chmod 754 $remotePath",
            $command,
            "rm $remotePath",
        ]);
    }

    private function runAsRoot($commands)
    {
        $output = '';
        $commands = (array) $commands;

        SSH::into('production_root')->run($commands, function($line) use (&$output)
        {
            $output .= $line;
            $this->explainer->line(trim($line));
        });

        return $output;
    }

    private function getSecretFromProductionEnvFile($key, $promptIfKeyMissing)
    {
        $this->loadSecretFileIfNeeded();

        if (!empty($this->secrets[$key]))
        {
            $this->explainer->line("$key found in secret file");

            return $this->secrets[$key];
        }

        $this->explainer->error("$key not found in secret file");

        return $this->command->askSecretly($promptIfKeyMissing);
    }

    private function loadSecretFileIfNeeded()
    {
        if ($this->secrets === false)
        {
            // Secret file not found
            return;
        }

        if ($this->secrets === null)
        {
            $envProductionPath = base_path('.env.production.php');

            if (!File::exists($envProductionPath))
            {
                $this->explainer->error("Secret file $envProductionPath not found");
                $this->secrets = false;
            }
            else
            {
                $this->explainer->line("Secret file found!");
                $this->secrets = require_once $envProductionPath;
            }
        }
    }

}
