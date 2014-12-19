<?php namespace Groupeat\Deploy\Commands;

use anlutro\cURL\cURL;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class OpCacheResetCommand extends Command {

    protected $name = 'groupeat:opcache';
    protected $description = 'Reset the OPcache on both server and CLI';


    public function fire()
    {
        $path = 'packages/groupeat/deploy/reset_opcache.php';
        $this->callSilent('asset:publish', ['--bench' => 'groupeat/deploy']);
        $this->resetServer($path);
        $this->resetCLI($path);
    }

    private function resetServer($path)
    {
        if (App::environment() == 'production')
        {
            $host = Config::get('remote.connections.production.host');
            $url = 'http://'.$host.'/'.$path;
        }
        else
        {
            $url = URL::to($path);
        }

        $this->line('Hitting '.$url);
        (new cURL)->get($url);
    }

    private function resetCLI($path)
    {
        $file = public_path($path);
        $this->line('Requiring '.$file);
        require $file;
    }

}
