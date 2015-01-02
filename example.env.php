<?php

/**
 * The fields that already have a vaild value are used on local or building environment.
 * In production environment, the value will be different for security reasons.
 */
return [

    'APP_KEY' => '0rhjhcJnDXSWaEKMgShrWHO2WGeAlty7',

    'PGSQL_PASSWORD' => 'groupeat',

    'ADMIN_KEY' => 'groupeat',

    'GANDI_MAIL_PASSWORD' => 'FILL_IF_YOU_WANT_TO_SEND_MAIL_OR_SETUP_PRODUCTION_SERVER',

    'GITHUB_PASSWORD' => 'FILL_ONLY_IF_YOU_WANT_TO_SETUP_PRODUCTION_SERVER',

    'PROD_PGSQL_PASSWORD' => 'FILL_ONLY_IF_YOU_WANT_TO_SETUP_PRODUCTION_SERVER',

    'PROD_APP_KEY' => 'FILL_ONLY_IF_YOU_WANT_TO_SETUP_PRODUCTION_SERVER',

    'PROD_SSL_PRIVATE_KEY' => 'FILL_ONLY_IF_YOU_WANT_TO_SETUP_PRODUCTION_SERVER',

    'PROD_SSL_CERTIFICATE' => 'FILL_ONLY_IF_YOU_WANT_TO_SETUP_PRODUCTION_SERVER',

];
