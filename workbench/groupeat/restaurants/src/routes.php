<?php

Route::model('restaurant', 'Groupeat\Restaurants\Entities\Restaurant');
Route::model('product', 'Groupeat\Restaurants\Entities\Product');

Route::api(['version' => 'v1', 'protected' => true], function () {
    $controller = 'Groupeat\Restaurants\Api\V1\RestaurantsController';

    Route::get('restaurantCategories', "$controller@categoriesIndex");

    Route::get('foodTypes', "$controller@foodTypesIndex");

    Route::get('products/{product}/formats', "$controller@productFormatsIndex");

    Route::group(['prefix' => 'restaurants'], function () use ($controller) {
        Route::get('/', "$controller@index");

        Route::group(['prefix' => '{restaurant}'], function () use ($controller) {
            Route::get('/', "$controller@show");

            Route::get('address', "$controller@showAddress");

            Route::get('products', "$controller@productsIndex");
        });
    });
});
