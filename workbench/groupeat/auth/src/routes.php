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

    Route::group(['prefix' => 'resetPassword/{token}'], function() use ($controller)
    {
        Route::get('/', [
            'as' => 'auth.showResetPasswordForm',
            'uses' => "$controller@showResetPasswordForm",
        ]);

        Route::post('/', [
            'as' => 'auth.resetPassword',
            'uses' => "$controller@resetPassword",
        ]);
    });
});

Route::api(['version' => 'v1'], function()
{
    Route::group(['prefix' => 'auth'], function()
    {
        $controller = 'Groupeat\Auth\Api\V1\AuthController';

        Route::put('token', "$controller@getToken");

        Route::post('token', "$controller@resetToken");

        Route::post('resetPassword', "$controller@sendResetPasswordLink");
    });
});
