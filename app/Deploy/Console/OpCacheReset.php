<?php
namespace Groupeat\Deploy\Console;

use anlutro\cURL\cURL;
use App;
use Groupeat\Support\Console\Abstracts\Command;

class OpCacheReset extends Command
{
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

        $this->line("Hitting $url");
        $request->send();
    }

    private function resetCLI()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    }
}
