<?php namespace Groupeat\Deploy\Console;

use Groupeat\Support\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class PushCommand extends Command {

	protected $name = 'push';
	protected $description = "Push the code to the Git repository";


	public function fire()
	{
        $message = str_replace('"',"'",$this->argument('message'));

        $this->process('git add -u .');
        $this->process('git add .');
        $this->process('git commit -m "'.$message.'"');
        $this->process('git push');
	}

	protected function getArguments()
	{
		return [
			['message', InputArgument::REQUIRED, 'The commit message.'],
		];
	}

}
