<?php

Route::get('admin/login', [
    'as' => 'admin.login',
    'uses' => 'Groupeat\Admin\Html\AdminController@login',
]);

Route::post('admin/check', [
    'before' => 'csrf',
    'as' => 'admin.check',
    'uses' => 'Groupeat\Admin\Html\AdminController@check',
]);

Route::group(['before' => 'admin'], function()
{
    $controller = 'Groupeat\Admin\Html\AdminController';

    Route::get('admin/logout', [
        'as' => 'admin.logout',
        'uses' => "$controller@logout",
    ]);

    Route::get('phpinfo', [
        'as' => 'admin.phpinfo',
        'uses' => "$controller@PHPinfo",
    ]);

    Route::get('db', [
        'as' => 'admin.db',
        'uses' => "$controller@db",
    ]);

    Route::get('docs', [
        'as' => 'admin.docs',
        'uses' => "$controller@docs",
    ]);
});
