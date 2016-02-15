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
    | Maximum Order Flow Duration in Minutes
    |--------------------------------------------------------------------------
    |
    | The expected maximum time in minutes that a customer cant take to
    | make an order (from restaurant choice, to chart submition).
    |
    */

    'maximum_order_flow_in_minutes' => 5,

    /*
    |--------------------------------------------------------------------------
    | Maximum Preparation Time in Minutes
    |--------------------------------------------------------------------------
    |
    | The duration in minutes that a restaurant can take to prepare a groupOrder.
    |
    */

    'maximum_preparation_time_in_minutes' => 35,

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
