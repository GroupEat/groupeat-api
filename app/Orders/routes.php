<?php

Route::model('order', 'Groupeat\Orders\Entities\Order');
Route::model('groupOrder', 'Groupeat\Orders\Entities\GroupOrder');

Route::group(['prefix' => 'api', 'middleware' => 'auth'], function () {
    Route::group(['prefix' => 'groupOrders'], function () {
        $controller = 'Groupeat\Orders\Http\V1\GroupOrdersController';

        Route::get('/', "$controller@index");

        Route::group(['prefix' => '{groupOrder}', 'before' => 'allowDifferentToken'], function () use ($controller) {
            Route::get('/', "$controller@show");

            Route::post('confirm', "$controller@confirm");
        });
    });

    Route::group(['prefix' => 'orders'], function () {
        $controller = 'Groupeat\Orders\Http\V1\OrdersController';

        Route::group(['prefix' => '{order}'], function () use ($controller) {
            Route::get('deliveryAddress', "$controller@showDeliveryAddress");

            Route::get('/', "$controller@show");
        });

        Route::post('/', "$controller@place");
    });
});