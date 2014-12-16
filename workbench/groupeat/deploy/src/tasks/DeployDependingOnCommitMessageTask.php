<?php namespace Groupeat\Deploy\Tasks;

use Illuminate\Support\Facades\App;
use Rocketeer\Abstracts\AbstractTask;

class DeployDependingOnCommitMessageTask extends AbstractTask {

    protected $local = true;
    protected $description = "Cancel deployment after building if commit message does not contain [deploy]";


    public function execute()
    {
        if (App::environment() == 'building')
        {
            $lastCommitMessage = processAtProjectRoot('git log -1 HEAD --pretty=format:%s')->getOutput();

            if (!str_contains($lastCommitMessage, '[deploy]'))
            {
                $message = '"'.$lastCommitMessage.'" does not contain [deploy]. Cancelling deployment...';

                $this->explainer->line($message);
                exit;
            }
        }
    }

}
