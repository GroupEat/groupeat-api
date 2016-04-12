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

    /*
    |--------------------------------------------------------------------------
    | Maximum Number of Risky Noisy Notifications to Send
    |--------------------------------------------------------------------------
    |
    | When sending notifications to encourage people to join a group order
    | it is not always possible to be sure of their precise location.
    | Some risky notifications will be sent to some customers even though
    | it's unclear they can actually join the group order.
    | The config value below indicates the maximum number of people will receive
    | such notifications.
    |
    */

    'maximum_number_of_risky_notifications' => env('MAXIMUM_NUMBER_OF_RISKY_NOTIFICATIONS', 42),

];
