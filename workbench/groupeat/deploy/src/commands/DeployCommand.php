<?php namespace Groupeat\Deploy\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class DeployCommand extends Command {

    protected $name = 'groupeat:deploy';
    protected $description = 'Deploys the website';


    public function fire()
    {
        if ($this->option('if-needed'))
        {
            $lastCommitMessage = processAtProjectRoot('git log -1 HEAD --pretty=format:%s')->getOutput();

            if (str_contains($lastCommitMessage, '[deploy]'))
            {
                $this->call('deploy:deploy', ['--verbose' => true]);
            }
            else
            {
                $this->comment('"'.$lastCommitMessage.'" does not contain [deploy]. Cancelling deployment...');
            }
        }
    }

    protected function getOptions()
    {
        return [
            ['if-needed', 'i', InputOption::VALUE_NONE, 'Deploy only if the commit message contains [deploy].', null],
        ];
    }

}
