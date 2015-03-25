<?php

use Groupeat\Devices\Http\V1\DevicesController;

Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('devices/platforms', DevicesController::class.'@platformsIndex');

        Route::group(['prefix' => 'customers/{customer}/devices'], function () {
            Route::post('/', DevicesController::class.'@attach');
        });
    });
});
