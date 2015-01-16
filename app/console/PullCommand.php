<?php

use Groupeat\Support\Console\Command;

class PullCommand extends Command {

	protected $name = 'pull';
	protected $description = "Pull the code from the Git repository and prepare the application";


	public function fire()
	{
        $this->line('Pulling from Git repository');
        $this->process('git pull');

        $this->line('Installing Composer dependencies');
        $this->process('composer install');

        $this->line('Install the database');
        $this->call('db:install', ['--with-seeds' => true, '--entries' => 10]);

        $this->line('Building assets');
        $this->call('asset:build', ['--install' => true]);
	}

}
