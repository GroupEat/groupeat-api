<?php

use Groupeat\Admin\Forms\ResetPasswordForm;
use Groupeat\Auth\Entities\UserCredentials;

Route::model('user', 'Groupeat\Auth\Entities\UserCredentials');

Route::group(['prefix' => 'auth'], function()
{
    $controller = 'Groupeat\Auth\Html\AuthController';

    Route::get('activate/{token}', [
        'as' => 'auth.activate',
        'uses' => "$controller@activate",
    ]);

    Route::get('reset-password/{token}', [
        'as' => 'auth.showResetPasswordForm',
        'uses' => "$controller@showResetPasswordForm",
    ]);

    Route::post('reset-password/{token}', [
        'as' => 'auth.resetPassword',
        'uses' => "$controller@resetPassword",
    ]);
});

Route::api(['version' => 'v1'], function()
{
    Route::group(['prefix' => 'auth'], function()
    {
        $controller = 'Groupeat\Auth\Api\V1\AuthController';

        Route::post('token', "$controller@refreshToken");

        Route::post('reset-password', "$controller@sendResetPasswordLink");
    });
});
