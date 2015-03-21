<?php

use Groupeat\Notifications\Http\V1\NotificationsController;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'notification', 'middleware' => 'auth'], function () {
        Route::get('/', NotificationsController::class.'@send');
    });
});
