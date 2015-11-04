<?php

return [

    'enabled' => env('NOTIFICATIONS_ENABLED', false),

    'gcmKey' => env('GCM_KEY', 'MISSING_GCM_KEY'),

    'apnsCertificatePath' => base_path('.apns.pem'),

    'apnsCertificatePassphrase' => env('APNS_CERTIFICATE_PASSPHRASE', 'MISSING_APNS_CERTIFICATE_PASSPHRASE'),

];
