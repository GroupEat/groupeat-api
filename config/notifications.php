<?php

return [

    'enabled' => env('NOTIFICATIONS_ENABLED', false),

    'gcm' => [
        'key' => env('GCM_KEY', 'MISSING_GCM_KEY'),
    ],

    'apns' => [
        'certificatePath' => base_path('.apns.pem'),
        'certificatePassphrase' => env('APNS_CERTIFICATE_PASSPHRASE', 'MISSING_APNS_CERTIFICATE_PASSPHRASE'),
    ],

];
