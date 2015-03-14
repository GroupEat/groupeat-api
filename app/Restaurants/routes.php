<?php

use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Http\V1\RestaurantsController;

Route::model('restaurant', Restaurant::class);
Route::model('product', Product::class);

Route::group(['prefix' => 'api', 'middleware' => 'auth'], function () {
    Route::get('restaurantCategories', RestaurantsController::class.'@categoriesIndex');
    Route::get('foodTypes', RestaurantsController::class.'@foodTypesIndex');
    Route::get('products/{product}/formats', RestaurantsController::class.'@productFormatsIndex');

    Route::group(['prefix' => 'restaurants'], function () {
        Route::get('/', RestaurantsController::class.'@index');

        Route::group(['prefix' => '{restaurant}'], function () {
            Route::get('/', RestaurantsController::class.'@show');
            Route::get('address', RestaurantsController::class.'@showAddress');
            Route::get('products', RestaurantsController::class.'@productsIndex');
        });
    });
});
