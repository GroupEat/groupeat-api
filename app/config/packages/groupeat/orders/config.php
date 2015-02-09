<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maximum Distance in Kilometers
    |--------------------------------------------------------------------------
    |
    | When asking group order around specific coordonates we will consider that
    | a group order can be joined if the distance between the first delivery
    | adress and the given coordonates is less than this one.
    |
    */

    'around_distance_in_kilometers' => 3,

    /*
    |--------------------------------------------------------------------------
    | Miminum FoodRush Duration in Minutes
    |--------------------------------------------------------------------------
    |
    | The duration in minutes that a FoodRush must exceed.
    |
    */

    'minimum_foodrush_in_minutes' => 5,

    /*
    |--------------------------------------------------------------------------
    | Maximum FoodRush Duration in Minutes
    |--------------------------------------------------------------------------
    |
    | The duration in minutes that a FoodRush should not exceed.
    |
    */

    'maximum_foodrush_in_minutes' => 60,

    /*
    |--------------------------------------------------------------------------
    | Maximum Preparation time in Minutes
    |--------------------------------------------------------------------------
    |
    | The duration in minutes that a restaurant can take to prepare a groupOrder.
    |
    */

    'maximum_preparation_time_in_minutes' => 45,

];
