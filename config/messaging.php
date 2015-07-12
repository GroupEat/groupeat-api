<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Android SMS Gateway config
    |--------------------------------------------------------------------------
    |
    | We use the https://smsgateway.me service as a cheap but efficient
    | way to send short mesagges to the users.
    |
    */

    'sms_gateway' => [
        'email' => env('SMS_GATEWAY_EMAIL', 'groupeat.dev@gmail.com'),
        'device' => env('SMS_GATEWAY_DEVICE', 10339),
        'password' => env('SMS_GATEWAY_PASSWORD'),
    ],

];
