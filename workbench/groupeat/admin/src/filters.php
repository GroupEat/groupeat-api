<?php

Route::filter('admin', function()
{
    if (!app('LoginAdminService')->check())
    {
        return Redirect::guest(route('admin.showLoginForm'));
    }
});
