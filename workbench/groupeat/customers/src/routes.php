<?php

Route::model('customer', 'Groupeat\Customers\Entities\Customer');

Route::api(['version' => 'v1'], function()
{
    Route::group(['prefix' => 'customers'], function()
    {
        $controller = 'Groupeat\Customers\Api\V1\CustomersController';

        Route::group(['protected' => false], function() use ($controller)
        {
            Route::post('/', "$controller@register");
        });

        Route::group(['protected' => true], function() use ($controller)
        {
            Route::get('{customer}', "$controller@show");

            Route::patch('{customer}', "$controller@update");

            Route::delete('{customer}', "$controller@unregister");
        });
    });
});
