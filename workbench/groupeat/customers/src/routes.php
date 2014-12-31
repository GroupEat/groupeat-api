<?php

Route::model('customer', 'Groupeat\Customers\Entities\Customer');

Route::api(['version' => 'v1', 'protected' => false], function()
{
    $controller = 'Groupeat\Customers\Api\V1\CustomersController@';

    Route::get('customers', $controller.'index');

    /**
     * @param email
     * @param password
     */
    Route::post('customers', $controller.'store');

    Route::delete('customer/{customer}', $controller.'destroy');
});
