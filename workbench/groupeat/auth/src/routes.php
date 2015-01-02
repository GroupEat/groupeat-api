<?php

use Groupeat\Auth\Entities\UserCredentials;

Route::model('user', 'Groupeat\Auth\Entities\UserCredentials');

Route::get('auth/activate/{code}', ['as' => 'auth.activation', function($code)
{
    App::make('ActivateUserService')->call($code);

    // TODO: Create real view.
    return 'Activated!';
}]);

Route::api(['version' => 'v1', 'protected' => false], function()
{
    /**
     * @param email
     * @param password
     *
     * @return authentication token
     */
    Route::post('auth/token', 'Groupeat\Auth\Api\V1\AuthController@token');
});
