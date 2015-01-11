<?php

use Groupeat\Auth\Entities\UserCredentials;

Route::model('user', 'Groupeat\Auth\Entities\UserCredentials');

Route::get('auth/activate/{code}', ['as' => 'auth.activation', function($code)
{
    App::make('ActivateUserService')->call($code);

    // TODO: I18n.
    return View::make('auth::activated', ['hideNavbar' => true]);
}]);

Route::api(['version' => 'v1'], function()
{
    Route::put('auth/token', 'Groupeat\Auth\Api\V1\AuthController@refreshToken');
});
