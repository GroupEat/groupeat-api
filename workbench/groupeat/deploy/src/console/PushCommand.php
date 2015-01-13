<?php namespace Groupeat\Deploy\Console;

use Groupeat\Support\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PushCommand extends Command {

	protected $name = 'push';
	protected $description = "Push the code to the Git repository";


	public function fire()
	{
        $message = str_replace('"',"'",$this->argument('message'));

        if (!$this->option('force'))
        {
            $this->comment('Running git status before pushing');
            $this->process('git status');

            if (!$this->confirm("Confirm push with message: \"$message\" ? (y|n) "))
            {
                $this->error('Cancelling push...');
                return;
            }

            $this->comment('Running all the tests');
            $testsPassing = $this->process('./vendor/bin/codecept run --fail-fast')->isSuccessful();

            if (!$testsPassing)
            {
                $this->error('At least one test failed: cancelling push...');
                return;
            }

            $this->info('All tests have passed');
        }
        
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

    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Force push without running the tests.', null],
        ];
    }

}
