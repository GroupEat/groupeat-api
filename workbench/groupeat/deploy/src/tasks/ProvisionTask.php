<?php namespace Groupeat\Deploy\Tasks;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\SSH;
use Rocketeer\Abstracts\AbstractTask;
use Illuminate\Support\Facades\Config;

class ProvisionTask extends AbstractTask {

    const ENV_FILE_TEMP_PATH = '/tmp/.env.production.php';

    protected $description = "Initialize the production server";


    public function execute()
    {
        $githubApi = $this->makeGitHubApi();

        $sslKey = $this->getSecretFromServerParams(
            'PROD_SSL_PRIVATE_KEY',
            'Enter the SSL key: '
        );

        $appKey = $this->askForAppKey('Choose a 32 character string for the application key: ');

        $postgresPassword = $this->getSecretFromServerParams(
            'PROD_PGSQL_PASSWORD',
            'Choose the password for the postgreSQL DB: '
        );

        $serverName = $this->getServerName();

        $this->addVagrantUserAndShippableKey($githubApi->getEmail(), Config::get('remote.shippable_key'));
        $githubApi->addSSHkey($this->getServerPublicKey(), $serverName);
        $this->provisionServer(
            Config::get('remote.domain'),
            $appKey,
            $postgresPassword,
            $githubApi->addOAuthToken($serverName)
        );
    }

    /**
     * @return \Groupeat\Deploy\Services\GitHubApi
     */
    private function makeGitHubApi()
    {
        $username = $this->getGitHubUsername();
        $passwordPrompt = ucfirst($username).', enter your GitHub password: ';
        $password = $this->getSecretFromServerParams('GITHUB_PASSWORD', $passwordPrompt);
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

    private function provisionServer($domain, $appKey, $postgresPassword, $githubOAuthToken)
    {
        $envFile = $this->getEnvProductionFileContent($appKey, $postgresPassword);
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
            $postgresPassword,
        ]);

        $this->runAsRoot([
            'echo "Adding GitHub token to Composer config"',
            'composer config -g github-oauth.github.com '.$githubOAuthToken,
        ]);
    }

    private function askForAppKey($question)
    {
        do
        {
            $key = $this->getSecretFromServerParams('PROD_APP_KEY', $question);
        } while (strlen($key) != '32');

        return $key;
    }

    private function getGitHubUsername()
    {
        $output = process('ssh -T git@github.com')->getErrorOutput();
        preg_match("/^hi (\w+)!/i", $output, $matches);

        if (!empty($matches[1]))
        {
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

    private function getEnvProductionFileContent($appKey, $postgresPassword)
    {
        return <<<EOD
<?php

return [
    'PGSQL_PASSWORD' => '$postgresPassword',
    'APP_KEY' => '$appKey',
];
EOD;
    }

    private function executeScriptRemotely($scriptName, $description, $arguments = [])
    {
        $localPath = base_path('scripts/'.$scriptName.'.sh');

        if (!file_exists($localPath))
        {
            $this->explainer->error('File '.$localPath.' do not exists.');
            exit;
        }

        $remotePath = '/tmp/'.$scriptName.'.sh';
        $this->putFile($remotePath, file_get_contents($localPath));
        $command = $remotePath.' '.implode(' ', $arguments);

        return $this->runAsRoot([
            'echo "'.$description.'"',
            'chmod 754 '.$remotePath,
            $command,
            'rm '.$remotePath,
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

    private function getSecretFromServerParams($key, $promptIfKeyMissing)
    {
        if (!empty($_SERVER[$key]))
        {
            return $_SERVER[$key];
        }

        $this->explainer->error('Cannot find key ['.$key.'] in $_SERVER params');

        return $this->askSecretly($promptIfKeyMissing);
    }

}
