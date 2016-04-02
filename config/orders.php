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
    | Duration in Minutes of an External Group Order Pushed by a Restaurant
    |--------------------------------------------------------------------------
    |
    | The duration in minutes during which the group order created by
    | a restaurant can be joined.
    |
    */

    'external_group_order_duration_in_minutes' => 10,

];
