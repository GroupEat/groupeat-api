<?php

use Groupeat\Customers\Http\V1\CustomersController;
use Groupeat\Customers\Http\V1\AddressesController;

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('predefinedAddresses', AddressesController::class.'@predefinedIndex');
    });

    $api->group(['prefix' => 'customers'], function ($api) {
        $api->post('/', CustomersController::class.'@register');

        $api->group(['middleware' => 'api.auth'], function ($api) {
            $api->group(['prefix' => '{customer}'], function ($api) {
                $api->get('/', CustomersController::class.'@show');
                $api->put('/', CustomersController::class.'@update');
                $api->delete('/', CustomersController::class.'@unregister');

                $api->group(['prefix' => 'address'], function ($api) {
                    $api->get('/', AddressesController::class.'@show');
                    $api->put('/', AddressesController::class.'@update');
                });
            });
        });
    });
});
