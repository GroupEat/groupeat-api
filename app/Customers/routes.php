<?php

use Groupeat\Customers\Http\V1\CustomersController;
use Groupeat\Customers\Http\V1\AddressesController;

Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('predefinedAddresses', AddressesController::class.'@predefinedIndex');
    });

    Route::group(['prefix' => 'customers'], function () {
        Route::post('/', CustomersController::class.'@register');

        Route::group(['middleware' => 'auth'], function () {
            Route::group(['prefix' => '{customer}'], function () {
                Route::get('/', CustomersController::class.'@show');
                Route::put('/', CustomersController::class.'@update');
                Route::delete('/', CustomersController::class.'@unregister');

                Route::group(['prefix' => 'address'], function () {
                    Route::get('/', AddressesController::class.'@show');
                    Route::put('/', AddressesController::class.'@update');
                });
            });
        });
    });
});
