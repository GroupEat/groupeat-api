<?php

return [

    // Remote server
    //////////////////////////////////////////////////////////////////////

    // Variables about the servers. Those can be guessed but in
    // case of problem it's best to input those manually
    'variables'      => [
        'directory_separator' => '/',
        'line_endings'        => "\n",
    ],

    // The number of releases to keep at all times
    'keep_releases'  => 4,

    // Folders
    ////////////////////////////////////////////////////////////////////

    // The root directory where your applications will be deployed
    // This path *needs* to start at the root, ie. start with a /
    'root_directory' => '/home/vagrant/',

    // The folder the application will be cloned in
    // Leave empty to use `application_name` as your folder name
    'app_directory'  => '',

    // A list of folders/file to be shared between releases
    // Use this to list folders that need to keep their state, like
    // user uploaded data, file-based databases, etc.
    'shared'         => [
        '.env',
        '.apns.pem',
        'storage/logs',
    ],

    // Execution
    //////////////////////////////////////////////////////////////////////

    // If enabled will force a shell to be created
    // which is required for some tools like RVM or NVM
    'shell'          => false,

    // An array of commands to run under shell
    'shelled'        => ['which', 'ruby', 'npm', 'bower', 'bundle', 'grunt'],

    // Permissions$
    ////////////////////////////////////////////////////////////////////

    'permissions'    => [

        // The folders and files to set as web writable
        // You can pass paths in brackets, so {path.public} will return
        // the correct path to the public folder
        'files'    => [
            'storage',
        ],

        // Here you can configure what actions will be executed to set
        // permissions on the folder above. The Closure can return
        // a single command as a string or an array of commands
        'callback' => function ($task, $file) {
            return [
                sprintf('chmod -R 775 %s', $file),
                sprintf('chmod -R g+s %s', $file),
                sprintf('chown -R vagrant:vagrant %s', $file),
            ];
        },

    ],

];
