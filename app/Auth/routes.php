<?php

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Http\V1\AuthController;

Route::model('user', UserCredentials::class);

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('activationTokens', AuthController::class.'@activate');
        Route::put('token', AuthController::class.'@getToken');
        Route::post('token', AuthController::class.'@resetToken');
        Route::delete('password', AuthController::class.'@sendPasswordResetLink');
        Route::post('password', AuthController::class.'@resetPassword');
        Route::put('password', AuthController::class.'@changePassword');
    });
});
