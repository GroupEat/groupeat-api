<?php

use Groupeat\Users\Models\User;

Route::api(['version' => 'v1', 'protected' => true], function ()
{
    Route::get('users', function()
    {
        return User::all();
    });
});
