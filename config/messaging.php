<?php

return [

    'enabled' => env('MESSAGING_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Nexmo API Config
    |--------------------------------------------------------------------------
    |
    | We use the Nexmo service as a reliable way to
    | send short mesagges to the users.
    |
    */

    'nexmo' => [
        'key' => env('NEXMO_KEY'),
        'secret' => env('NEXMO_SECRET'),
    ],

];
