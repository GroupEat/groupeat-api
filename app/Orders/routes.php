<?php

use Groupeat\Orders\Http\V1\ExternalOrdersController;
use Groupeat\Orders\Http\V1\GroupOrdersController;
use Groupeat\Orders\Http\V1\OrdersController;

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('customers/{customer}/orders', OrdersController::class.'@indexForCustomer');
        $api->get(
            'customers/{customer}/groupOrders/{groupOrder}/orders',
            OrdersController::class.'@indexForGroupOrder'
        );

        $api->group(['prefix' => 'groupOrders'], function ($api) {
            $api->get('/', GroupOrdersController::class.'@index');
        });

        $api->group(['prefix' => 'orders'], function ($api) {
            $api->group(['prefix' => '{order}'], function ($api) {
                $api->get('deliveryAddress', OrdersController::class.'@showDeliveryAddress');
                $api->get('/', OrdersController::class.'@show');
            });

            $api->post('/', OrdersController::class.'@place');
        });

        $api->group(['prefix' => 'restaurants/{restaurant}'], function ($api) {
            $api->get('groupOrders', GroupOrdersController::class.'@indexForRestaurant');
            $api->post('externalOrders', ExternalOrdersController::class.'@push');
        });
    });

    $api->group(['prefix' => 'groupOrders/{groupOrder}'], function ($api) {
        $api->group(['middleware' => ['api.auth']], function ($api) {
            $api->get('/', GroupOrdersController::class.'@show');
            $api->post('confirm', GroupOrdersController::class.'@confirm');
        });

        $api->group(['middleware' => ['api.auth']], function ($api) {
            $api->post('orders', GroupOrdersController::class.'@join');
        });
    });
});
