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

    'around_distance_in_kilometers' => 7.0,

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

    /*
    |--------------------------------------------------------------------------
    | Discount Rates
    |--------------------------------------------------------------------------
    |
    | The discount policy of a restaurant are saved in the database as an
    | array that represent the price to reach to unlock a specific discount rate.
    | The different discount rates are given here.
    |
    | Example: if the array below is [0.0, 10.0, 20.0, 30.0, 40.0, 50.0] and the restaurant
    | discount prices are [9, 10, 20, 25, 35, 60], it means that for 10e there
    | will be a 10% discount, for 20e 20%, for 25e 30%, for 35e 40% and for 60e 50%.
    | From 0e to 9e there won't be any discount. Between the given points, the discount
    | rate increase linearly.
    |
    */

    'discountRates' => [0.0, 10.0, 20.0, 30.0, 40.0, 50.0],

];
