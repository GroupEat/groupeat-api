<?php namespace Groupeat\Core\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PushCommand extends Command {

	protected $name = 'groupeat:push';
	protected $description = 'Push the code to the Git repository';


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
