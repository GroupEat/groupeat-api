<?php

use Groupeat\Users\Models\User;

Route::api('v1', function ()
{
    Route::get('users', function()
    {
        return User::all();
    });
});
