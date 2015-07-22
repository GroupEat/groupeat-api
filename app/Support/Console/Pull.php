<?php
namespace Groupeat\Support\Console;

use Groupeat\Support\Console\Abstracts\Command;

class Pull extends Command
{
    protected $signature = 'pull';
    protected $description = "Pull the latest changes from the repo and install everything needed";

    public function handle()
    {
        $this->process('git pull');
        $this->process('composer install');
        $this->process('php artisan migrate:refresh --seed');
    }
}
