<?php namespace Groupeat\Support\Console;

class PullCommand extends Command {

	protected $name = 'pull';
	protected $description = "Pull the code from the Git repository and install the dependencies";


	public function fire()
	{
        $this->process('git pull');
        $this->process('composer install');
	}

}
