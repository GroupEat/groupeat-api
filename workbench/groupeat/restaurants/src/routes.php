<?php

Route::model('restaurant', 'Groupeat\Restaurants\Entities\Restaurant');

Route::api(['version' => 'v1'], function()
{
    $controller = 'Groupeat\Restaurants\Api\V1\RestaurantsController';

    Route::get('food-types', "$controller@foodTypesIndex");

    Route::group(['prefix' => 'restaurants'], function() use ($controller)
    {
        Route::get('/', "$controller@index");

        Route::get('{restaurant}/address', "$controller@showAddress");
    });
});
