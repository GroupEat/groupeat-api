<?php

use Rocketeer\Facades\Rocketeer;

Rocketeer::listenTo('deploy.before-symlink', function ($task) {
    $path = $task->releasesManager->getCurrentReleasePath();

    foreach (['optimize', 'opcache'] as $command) {
        $task->run("cd $path; php artisan $command");
    }
});
