<?php
namespace Groupeat\Support\Console;

use App;
use Groupeat\Support\Console\Abstracts\Command;
use Symfony\Component\Console\Input\InputOption;

class Helpers extends Command
{
    protected $name = 'helpers';
    protected $description = "Generate various helper files to improve IDE autocompletion";

    public function fire()
    {
        if (App::isLocal()) {
            $this->call('ide-helper:generate');
            $this->call('ide-helper:meta');
            $this->call('ide-helper:models', ['--nowrite' => true]);
        } else {
            $this->comment("Skipping helper files generation on this environment.");
        }
    }
}
