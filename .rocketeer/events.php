<?php

use Rocketeer\Facades\Rocketeer;

Rocketeer::listenTo('deploy.before-symlink', function ($task) {
    $commands = [
        'optimize',
        'route:cache',
        'config:cache',
        'opcache:clear',
        'docs',
        'adminer',
        'db:backup --s3',
    ];

    array_map(function($command) use ($task) {
        $task->runForCurrentRelease("php artisan $command");

        if (!$task->status()) {
            $task->explainer->error("Cancelling release because command '$command' failed");
            exit(1);
        }
    }, $commands);
});
