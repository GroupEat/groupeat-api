<?php

use Groupeat\Settings\Http\V1\SettingsController;

Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['prefix' => 'customers/{customer}/settings'], function () {
            Route::get('/', SettingsController::class.'@index');
            Route::put('/', SettingsController::class.'@update');
        });
    });
});
