<?php

use Groupeat\Auth\Entities\UserCredentials;

Route::model('user', 'Groupeat\Auth\Entities\UserCredentials');

Route::group(['prefix' => 'auth'], function()
{
    Route::get('activate/{code}', ['as' => 'auth.activation', function($code)
    {
        App::make('ActivateUserService')->call($code);

        // TODO: I18n.
        return View::make('auth::activated', ['hideNavbar' => true]);
    }]);

//    Route::get('reset-password/{code}', ['as' => 'auth.resetPassword', function($code)
//    {
//
//    }]);
});

Route::api(['version' => 'v1'], function()
{
    Route::group(['prefix' => 'auth'], function()
    {
        Route::put('token', 'Groupeat\Auth\Api\V1\AuthController@refreshToken');
    });
});
