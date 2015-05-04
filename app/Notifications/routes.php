<?php

use Groupeat\Notifications\Http\V1\NotificationsController;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'notifications', 'middleware' => 'auth'], function () {
        Route::post('/', NotificationsController::class.'@send');
    });
});
