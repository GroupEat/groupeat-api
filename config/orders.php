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

    'joinable_distance_in_kilometers' => 3.0,

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
    | Time to Confirm in Minutes
    |--------------------------------------------------------------------------
    |
    | When a group order is complete, the restaurant should quickly confirm it and
    | indicate its preparation time.
    |
    */

    'time_to_confirm_in_minutes' => 10,

    /*
    |--------------------------------------------------------------------------
    | Maximum Preparation Time in Minutes
    |--------------------------------------------------------------------------
    |
    | The duration in minutes that a restaurant can take to prepare a groupOrder.
    |
    */

    'maximum_preparation_time_in_minutes' => 45,

    /*
    |--------------------------------------------------------------------------
    | Foodrush Duration in Minutes when External Order is Pushed by Restaurant
    |--------------------------------------------------------------------------
    |
    | The duration in minutes of the foodrush created when a restaurant push
    | an external order into the application.
    |
    */

    'external_order_foodrush_in_minutes' => 10,

];
