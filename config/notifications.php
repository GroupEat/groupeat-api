<?php

return [

    'enabled' => env('NOTIFICATIONS_ENABLED', false),

    'gcm' => [
        'key' => env('GCM_KEY', 'MISSING_GCM_KEY'),
    ],

    'apns' => [
        'certificate_passphrase' => env('APNS_CERTIFICATE_PASSPHRASE', 'MISSING_APNS_CERTIFICATE_PASSPHRASE'),
        'certificate_path' => base_path('.apns.pem'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Device Location Freshness in Minutes
    |--------------------------------------------------------------------------
    |
    | The duration in minutes during which a device location is considered
    | relevant. Once this period has passed, the location won't be exploited.
    |
    */

    'device_location_freshness_in_minutes' => 60 * 24,

];
