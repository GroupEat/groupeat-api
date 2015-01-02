<?php

use Groupeat\Support\Exceptions\Unauthorized;

Route::filter('admin', function()
{
    if (App::environment('production') && Input::get('admin_key') != $_SERVER['ADMIN_KEY'])
    {
        throw new Unauthorized("Missing administrator key.");
    }
});

Route::when('admin/*', 'admin');
