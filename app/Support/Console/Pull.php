<?php
namespace Groupeat\Support\Console;

use Groupeat\Support\Console\Abstracts\Command;
use Symfony\Component\Console\Input\InputOption;

class Pull extends Command
{
    protected $name = 'pull';
    protected $description = "Pull the latest changes from the repo and install everything needed";

    public function fire()
    {
        $this->process('git pull');
        $this->process('composer install');
        $this->process('php artisan db:install --seed');
    }
}
