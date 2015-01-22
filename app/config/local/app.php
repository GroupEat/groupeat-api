<?php

return [
	'debug' => true,
    'url' => 'https://groupeat.dev',

    'providers' => append_config([
        'Illuminate\Foundation\Providers\TinkerServiceProvider',

        'Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider',
        'Clockwork\Support\Laravel\ClockworkServiceProvider',
        'Rocketeer\RocketeerServiceProvider',
    ]),
];
