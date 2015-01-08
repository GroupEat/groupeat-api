<?php

Route::get('admin/login', function()
{
    return View::make('admin::login', ['hideNavbar' => true]);
});

Route::post('admin/check', function()
{
    if (App::make('LoginAdminService')->attempt(Input::get('email'), Input::get('password')))
    {
        return Redirect::to('phpinfo');
    }

    return Redirect::to('admin/login');
});

Route::group(['before' => 'admin'], function()
{
    $controller = 'Groupeat\Admin\Html\AdminController';

    Route::get('phpinfo', "$controller@PHPinfo");

    Route::get('db', "$controller@db");

    Route::get('docs', "$controller@docs");
});
