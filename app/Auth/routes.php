<?php

use Groupeat\Auth\Http\V1\AuthController;

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'auth'], function ($api) {
        $api->post('activationTokens', AuthController::class.'@activate');
        $api->put('token', AuthController::class.'@getToken');
        $api->post('token', AuthController::class.'@resetToken');
        $api->post('passwordResets', AuthController::class.'@sendPasswordResetLink');
        $api->post('password', AuthController::class.'@resetPassword');
        $api->put('password', AuthController::class.'@changePassword');
    });
});
