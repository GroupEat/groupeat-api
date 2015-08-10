<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Account Password
    |--------------------------------------------------------------------------
    |
    | When seeding the database, a default admin account is created so that
    | it is always possible to acces the admin zone.
    | Specify here the password of this account.
    |
    */

    'default_admin_password' => env('DEFAULT_ADMIN_PASSWORD', 'groupeat'),

    /*
    |--------------------------------------------------------------------------
    | Max Confirmation Time in Minutes
    |--------------------------------------------------------------------------
    |
    | When a group order is closed, the restaurant receive a mail asking
    | when it will be prepared. We want to check that he actually
    | submit this form quickly. The value below is the number of minutes
    | he has between the sending of the mail and the group order confirmation.
    |
    */

    'max_confirmation_duration_in_minutes' => 5,

];
