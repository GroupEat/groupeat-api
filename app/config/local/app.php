<?php

return [
	'debug' => true,
    'url' => 'https://groupeat.dev',

    'providers' => append_config([
        'Illuminate\Foundation\Providers\TinkerServiceProvider',

        'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
        'Rocketeer\RocketeerServiceProvider',
    ]),
];
