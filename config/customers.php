<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Constraint Address data
    |--------------------------------------------------------------------------
    |
    | For the MVP, the customers must live on the Campus.
    | We therefore have to force some fields of their address.
    |
    */

    'address_constraints' => [
        'city' => 'Palaiseau',
        'postcode' => 91120,
        'state' => 'Essone',
        'country' => 'France',
    ],

];
