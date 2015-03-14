<?php

use Groupeat\Orders\Http\V1\GroupOrdersController;
use Groupeat\Orders\Http\V1\OrdersController;

Route::model('order', \Groupeat\Orders\Entities\Order::class);
Route::model('groupOrder', \Groupeat\Orders\Entities\GroupOrder::class);

Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['prefix' => 'groupOrders'], function () {
            Route::get('/', GroupOrdersController::class.'@index');
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::group(['prefix' => '{order}'], function () {
                Route::get('deliveryAddress', OrdersController::class.'@showDeliveryAddress');
                Route::get('/', OrdersController::class.'@show');
            });

            Route::post('/', OrdersController::class.'@place');
        });
    });

    Route::group(['prefix' => 'groupOrders/{groupOrder}', 'middleware' => 'allowDifferentToken'], function () {
        Route::get('/', GroupOrdersController::class.'@show');
        Route::post('confirm', GroupOrdersController::class.'@confirm');
    });
});
