<?php

return [

	// Tasks
	//
	// Here you can define in the `before` and `after` array, Tasks to execute
	// before or after the core Rocketeer Tasks. You can either put a simple command,
	// a closure which receives a $task object, or the name of a class extending
	// the Rocketeer\Abstracts\AbstractTask class
	//
	// In the `custom` array you can list custom Tasks classes to be added
	// to Rocketeer. Those will then be available in the command line
	// with all the other tasks
	//////////////////////////////////////////////////////////////////////

	// Tasks to execute before the core Rocketeer Tasks
	'before' => [
		'setup'   => [
            function($task)
            {
                $host = $task->connections->getConnectionCredentials()[0]['host'];

                $task->onLocal(function($localTask) use ($host)
                {
                    $localTask->run('ssh-keygen -R '.$host);
                });
            },
            'Groupeat\Deploy\Tasks\ProvisionTask',
		],
		'deploy'  => [
            'Groupeat\Deploy\Tasks\DeployDependingOnCommitMessageTask',
        ],
		'cleanup' => [],
	],

	// Tasks to execute after the core Rocketeer Tasks
	'after'  => [
		'setup'   => [
            function($task)
            {
                $sharedFolder = $task->paths->getFolder('shared');
                $task->run('mv '.Groupeat\Deploy\Tasks\ProvisionTask::ENV_FILE_TEMP_PATH.' '.$sharedFolder);
            },
        ],
		'deploy'  => [
            'php artisan optimize',
            'php artisan opcache',
            function($task)
            {
                // TODO: Remove this before app launch.
                // This command need to be executed in another thread in order to work while deploying.
                $migrationCommand = 'php artisan db-install --force --with-seeds --entries 100';
                processAtProjectRoot($migrationCommand, $task->command->getOutput());
            },
        ],
		'cleanup' => [],
	],

	// Custom Tasks to register with Rocketeer
	'custom' => [],

];
