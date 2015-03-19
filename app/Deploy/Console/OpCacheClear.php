<?php
namespace Groupeat\Deploy\Console;

use Groupeat\Support\Console\Abstracts\Command;
use GuzzleHttp\Client;

class OpCacheClear extends Command
{
    protected $name = 'opcache:clear';
    protected $description = "Clear the OPcache on both server and CLI";

    public function fire()
    {
        $this->clearServer();
        $this->clearCLI();
    }

    private function clearServer()
    {
        $url = url('api/deploy/opcache');
        $this->line("Hitting $url");
        $this->line((string) (new Client)->delete($url)->getBody());
    }

    private function clearCLI()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
            $this->line("Opcache has been cleared for CLI.");
        } else {
            $this->comment("Opcache not enabled for CLI.");
        }
    }
}
