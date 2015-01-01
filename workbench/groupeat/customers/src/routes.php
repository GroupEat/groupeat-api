<?php

Route::model('customer', 'Groupeat\Customers\Entities\Customer');

Route::api(['version' => 'v1'], function()
{
    $controller = 'Groupeat\Customers\Api\V1\CustomersController@';

    Route::group(['protected' => false], function() use ($controller)
    {
        /**
         * @param email
         * @param password
         *
         * @return string The authentication token
         */
        Route::post('customers', $controller.'store');
    });

    Route::group(['protected' => true], function() use ($controller)
    {
        Route::get('customers', $controller.'index');

        Route::delete('customer/{customer}', $controller.'destroy');
    });
});
