<?php

Route::model('order', 'Groupeat\Orders\Entities\Order');
Route::model('groupOrder', 'Groupeat\Orders\Entities\GroupOrder');

Route::group(['prefix' => 'groupOrders/{groupOrder}'], function()
{
    $controller = 'Groupeat\Orders\Html\GroupOrdersController';

    Route::group(['prefix' => 'confirm/{token}'], function() use ($controller)
    {
        Route::get('/', ['as' => 'orders.confirmGroupOrder', 'uses' => "$controller@showConfirmForm"]);
        Route::post('/', "$controller@confirm");
    });
});

Route::api(['version' => 'v1', 'protected' => true], function()
{
    Route::group(['prefix' => 'groupOrders'], function()
    {
        $controller = 'Groupeat\Orders\Api\V1\GroupOrdersController';

        Route::get('{groupOrder}', "$controller@show");

        Route::get('/', "$controller@index");
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
