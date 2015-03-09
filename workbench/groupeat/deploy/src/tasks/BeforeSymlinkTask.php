<?php
namespace Groupeat\Deploy\Tasks;

use Rocketeer\Abstracts\AbstractTask;

class BeforeSymlinkTask extends AbstractTask
{
    protected $description = "Execute some tasks before symlinking the release";

    public function execute()
    {
        $path = $this->releasesManager->getCurrentReleasePath();

        foreach (['optimize', 'opcache'] as $command) {
            $this->run("cd $path; php artisan $command");
        }
    }
}
