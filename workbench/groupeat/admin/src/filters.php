<?php

Route::filter('admin', function()
{
    if (!App::make('LoginAdminService')->check())
    {
        return Redirect::to('admin/login');
    }
});
