<?php namespace Groupeat\Deploy\Console;

use anlutro\cURL\cURL;
use App;
use Illuminate\Console\Command;
use URL;

class OpCacheResetCommand extends Command {

    protected $name = 'opcache';
    protected $description = "Reset the OPcache on both server and CLI";


    public function fire()
    {
        $path = 'packages/groupeat/deploy/reset_opcache.php';
        $this->call('asset:publish', ['--bench' => 'groupeat/deploy']);
        $this->resetServer($path);
        $this->resetCLI($path);
    }

    private function resetServer($path)
    {
        $url = URL::to($path);
        $request = (new cURL)->newRequest('GET', $url);

        if (App::isLocal())
        {
            $this->comment('Disabling cURL SSL certificate verification');
            $request->setOptions([
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
            ]);
        }

        $this->line("Hitting $url");
        $request->send();
    }

    private function resetCLI($path)
    {
        $file = public_path($path);
        $this->line("Requiring $file");

        require $file;
    }

}
