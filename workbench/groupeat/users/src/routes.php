<?php

use Groupeat\Users\Models\User;

Route::get('users', function()
{
    return User::all();
});
