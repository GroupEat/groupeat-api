<?php

Route::api(['version' => 'v1', 'protected' => true], function()
{
    $controller = 'Groupeat\Orders\Api\V1\GroupOrdersController';

    Route::get('group-orders', "$controller@index");

    Route::post('orders', "$controller@placeOrder");
});
