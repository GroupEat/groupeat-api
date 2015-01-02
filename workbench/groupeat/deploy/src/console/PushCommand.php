<?php namespace Groupeat\Deploy\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PushCommand extends Command {

	protected $name = 'push';
	protected $description = "Push the code to the Git repository";


	public function fire()
	{
        $message = str_replace('"',"'",$this->argument('message'));

        $this->line(system('git add -u .'));
        $this->line(system('git add .'));
        $this->line(system('git commit -m "'.$message.'"'));
        $this->line(system('git push'));
	}

	protected function getArguments()
	{
		return [
			['message', InputArgument::REQUIRED, 'The commit message.'],
		];
	}

}
