<?php

use Groupeat\Auth\Entities\UserCredentials;

Route::model('user', 'Groupeat\Auth\Entities\UserCredentials');

Route::api(['version' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        $controller = 'Groupeat\Auth\Api\V1\AuthController';

        Route::post('activationTokens', "$controller@activate");

        Route::put('token', "$controller@getToken");

        Route::post('token', "$controller@resetToken");

        Route::delete('password', "$controller@sendPasswordResetLink");

        Route::post('password', "$controller@resetPassword");
    });
});
