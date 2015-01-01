<?php

use Groupeat\Auth\Entities\UserCredentials;

Route::model('user', 'Groupeat\Auth\Entities\UserCredentials');

Route::get('users/{user}/activate/{code}', function(UserCredentials $userCredentials, $code)
{
    $activationService = App::make('ActivateUserService');

    if (!$activationService->call($userCredentials, $code))
    {
        // TODO create real view
        return App::abort(500, $activationService->errors()->first());
    }

    // TODO create real view
    return 'Activated!';
});

Route::api(['version' => 'v1', 'protected' => false], function()
{
    Route::group([], function()
    {
        /**
         * @param email
         * @param password
         *
         * @return string The authentication token
         */
        Route::post('auth/token', 'Groupeat\Auth\Api\V1\AuthController@token');
    });
});
