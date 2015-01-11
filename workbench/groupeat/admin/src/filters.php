<?php

Route::filter('admin', function()
{
    if (!App::make('LoginAdminService')->check())
    {
        return Redirect::guest(URL::route('admin.login'));
    }
});
