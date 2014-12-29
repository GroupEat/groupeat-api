<?php

return [
	'debug' => true,
    'url' => 'http://groupeat.dev',

    'providers' => append_config([
        'Illuminate\Foundation\Providers\TinkerServiceProvider',
        'Rocketeer\RocketeerServiceProvider',
    ]),
];
