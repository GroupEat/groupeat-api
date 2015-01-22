<?php

Route::model('restaurant', 'Groupeat\Restaurants\Entities\Restaurant');

Route::api(['version' => 'v1', 'protected' => true], function()
{
    $controller = 'Groupeat\Restaurants\Api\V1\RestaurantsController';

    Route::get('restaurant-categories', "$controller@categoriesIndex");

    Route::group(['prefix' => 'restaurants'], function() use ($controller)
    {
        Route::get('/', "$controller@index");
    });
});
