<?php

use Groupeat\Support\Exceptions\Unauthorized;

Route::filter('admin', function()
{
    // On local environment, the admin routes are always opened.
    // On other environments, you need to give the admin_key to be granted access.
    if (App::isLocal() || (Input::get('admin_key') == $_SERVER['ADMIN_KEY']))
    {
        Session::put('admin', true);
    }

    if (!Session::get('admin'))
    {
        throw new Unauthorized("Missing administrator key.");
    }
});
