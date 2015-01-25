<?php

Route::api(['version' => 'v1', 'protected' => true], function()
{
    $controller = 'Groupeat\Orders\Api\V1\OrdersController';

    Route::post('orders', "$controller@placeOrder");
});
