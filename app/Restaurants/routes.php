<?php

use Groupeat\Restaurants\Http\V1\RestaurantsController;

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('products/{product}/formats', RestaurantsController::class.'@productFormatsIndex');

        $api->group(['prefix' => 'restaurants'], function ($api) {
            $api->get('/', RestaurantsController::class.'@index');

            $api->group(['prefix' => '{restaurant}'], function ($api) {
                $api->get('/', RestaurantsController::class.'@show');
                $api->get('address', RestaurantsController::class.'@showAddress');
                $api->get('products', RestaurantsController::class.'@productsIndex');
            });
        });
    });
});
