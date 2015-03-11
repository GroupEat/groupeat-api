<?php

Route::model('customer', 'Groupeat\Customers\Entities\Customer');

Route::group(['prefix' => 'api'], function () {
    $controller = 'Groupeat\Customers\Http\V1\CustomersController';

    Route::group(['middleware' => 'auth'], function () use ($controller) {
        Route::get('predefinedAddresses', 'Groupeat\Customers\Http\V1\AddressesController@predefinedIndex');
    });

    Route::group(['prefix' => 'customers'], function () use ($controller) {
        Route::post('/', "$controller@register");

        Route::group(['middleware' => 'auth'], function () use ($controller) {
            Route::group(['prefix' => '{customer}'], function () use ($controller) {
                Route::get('/', "$controller@show");

                Route::put('/', "$controller@update");

                Route::delete('/', "$controller@unregister");

                Route::group(['prefix' => 'address'], function () use ($controller) {
                    Route::get('/', 'Groupeat\Customers\Http\V1\AddressesController@show');

                    Route::put('/', 'Groupeat\Customers\Http\V1\AddressesController@change');
                });
            });
        });
    });
});
