<?php namespace Groupeat\Deploy\Tasks;

use App;
use Rocketeer\Abstracts\AbstractTask;

class DeployDependingOnCommitMessageTask extends AbstractTask {

    protected $local = true;
    protected $description = "Cancel deployment after building if commit message contains [skip deploy]";


    public function execute()
    {
        if (App::environment('building'))
        {
            $lastCommitMessage = processAtProjectRoot('git log -1 HEAD --pretty=format:%s')->getOutput();

            if (str_contains($lastCommitMessage, '[skip deploy]'))
            {
                $message = '"'.$lastCommitMessage.'" contains [skip deploy]. Cancelling deployment...';

                $this->explainer->line($message);

                exit;
            }
        }
    }

}
