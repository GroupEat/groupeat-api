<?php

return [
    'default' => 'testing',

    'connections' => [
        'setup' => [
            'driver' => 'sqlite',
            'database' => __DIR__.'/../../../tests/_data/setup.sqlite',
            'prefix' => '',
        ],

        'testing' => [
            'driver'   => 'sqlite',
            'database' => __DIR__.'/../../../tests/_data/testing.sqlite',
            'prefix'   => '',
        ],
    ],
];
