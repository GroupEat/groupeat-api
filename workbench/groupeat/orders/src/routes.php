<?php

Route::model('order', 'Groupeat\Orders\Entities\Order');
Route::model('groupOrder', 'Groupeat\Orders\Entities\GroupOrder');

Route::api(['version' => 'v1', 'protected' => true], function()
{
    Route::group(['prefix' => 'groupOrders'], function()
    {
        $controller = 'Groupeat\Orders\Api\V1\GroupOrdersController';

        Route::get('/', "$controller@index");

        Route::group(['prefix' => '{groupOrder}', 'before' => 'allowDifferentToken'], function() use ($controller)
        {
            Route::get('/', "$controller@show");

            Route::post('confirm', "$controller@confirm");
        });
    });

    Route::group(['prefix' => 'orders'], function()
    {
        $controller = 'Groupeat\Orders\Api\V1\OrdersController';

        Route::group(['prefix' => '{order}'], function() use ($controller)
        {
            Route::get('deliveryAddress', "$controller@showDeliveryAddress");

            Route::get('/', "$controller@show");
        });

        Route::post('/', "$controller@place");
    });
});
