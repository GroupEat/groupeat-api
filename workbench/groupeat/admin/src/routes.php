<?php

Route::get('admin/login', [
    'as' => 'admin.showLoginForm',
    'uses' => 'Groupeat\Admin\Html\AdminController@showLoginForm',
]);

Route::post('admin/login', [
    'before' => 'csrf',
    'as' => 'admin.loginCheck',
    'uses' => 'Groupeat\Admin\Html\AdminController@loginCheck',
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
