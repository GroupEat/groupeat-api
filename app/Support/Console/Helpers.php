<?php
namespace Groupeat\Support\Console;

use App;
use Groupeat\Support\Console\Abstracts\Command;
use Symfony\Component\Console\Input\InputOption;

class Helpers extends Command
{
    protected $signature = 'helpers';
    protected $description = "Generate various helper files to improve IDE autocompletion";

    public function handle()
    {
        if (App::isLocal()) {
            $this->call('ide-helper:generate');
            $this->call('ide-helper:models', ['--nowrite' => true]);
            $this->call('ide-helper:meta');
        } else {
            $this->comment('Skipping helper files generation on this environment.');
        }
    }
}
