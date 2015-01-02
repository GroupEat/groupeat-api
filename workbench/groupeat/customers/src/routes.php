<?php

Route::model('customer', 'Groupeat\Customers\Entities\Customer');

Route::api(['version' => 'v1'], function()
{
    Route::group(['prefix' => 'customers'], function()
    {
        $controller = 'Groupeat\Customers\Api\V1\CustomersController@';

        Route::group(['protected' => false], function() use ($controller)
        {
            /**
             * @param email
             * @param password
             */
            Route::post('/', $controller.'store');
        });

        Route::group(['protected' => true], function() use ($controller)
        {
            Route::get('/', $controller.'index');

            Route::get('me', $controller.'showCurrentUser');

            Route::delete('me', $controller.'destroyCurrentUser');
        });
    });
});