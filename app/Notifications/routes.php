<?php

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'notifications', 'middleware' => 'auth'], function () {
        $controller = 'Groupeat\Notifications\Http\V1\NotificationsController';

        Route::post('/', "$controller@saveRegistrationId");
        Route::get('/', "$controller@sendNotification");
    });
});
