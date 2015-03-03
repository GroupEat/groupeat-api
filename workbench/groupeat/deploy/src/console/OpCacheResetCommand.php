<?php namespace Groupeat\Deploy\Console;

use anlutro\cURL\cURL;
use App;
use Groupeat\Support\Console\Command;

class OpCacheResetCommand extends Command {

    protected $name = 'opcache';
    protected $description = "Reset the OPcache on both server and CLI";


    public function fire()
    {
        $this->resetServer();
        $this->resetCLI();
    }

    private function resetServer()
    {
        $url = url('api/deploy/opcache/reset');
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

    private function resetCLI()
    {
        if (function_exists('opcache_reset'))
        {
            opcache_reset();
        }
    }

}
