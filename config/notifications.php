<?php

return [

    'gcmApiKey' => env('GCM_API_KEY', 'MISSING_GCM_API_KEY'),

    'apnsCertificatePath' => base_path('.apns.pem'),

    'apnsCertificatePassphrase' => env('APNS_CERTIFICATE_PASSPHRASE', 'MISSING_APNS_CERTIFICATE_PASSPHRASE'),

];
