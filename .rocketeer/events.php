<?php

use Rocketeer\Facades\Rocketeer;

Rocketeer::listenTo('deploy.before-symlink', function ($task) {
    $path = $task->releasesManager->getCurrentReleasePath();

    foreach (['optimize', 'route:cache', 'config:cache', 'opcache:clear'] as $command) {
        $task->run("cd $path; php artisan $command");
    }
});
