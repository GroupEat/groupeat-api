<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum Restaurant Around Distance in Kilometers
    |--------------------------------------------------------------------------
    |
    | When asking restaurants around specific coordonates we will consider that
    | a restaurant is close enough if the distance is less thant this one.
    |
    */

    'around_distance_in_kilometers' => 10,

    /*
    |--------------------------------------------------------------------------
    | Minimum Restaurant Opening Duration in Minutes
    |--------------------------------------------------------------------------
    |
    | When asking which restaurants are opened we will consider that a
    | a restaurant is opened if it will stay open for this amount of minutes.
    |
    */

    'opening_duration_in_minutes' => 30,

];
