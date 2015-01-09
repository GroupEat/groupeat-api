<?php

Route::get('admin/login', 'Groupeat\Admin\Html\AdminController@login');

Route::post('admin/check', ['before' => 'csrf', 'uses' => 'Groupeat\Admin\Html\AdminController@check']);

Route::group(['before' => 'admin'], function()
{
    $controller = 'Groupeat\Admin\Html\AdminController';

    Route::get('admin/logout', "$controller@logout");

    Route::get('phpinfo', "$controller@PHPinfo");

    Route::get('db', "$controller@db");

    Route::get('docs', "$controller@docs");
});
