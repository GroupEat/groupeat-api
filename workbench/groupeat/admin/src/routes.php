<?php

Route::group(['before' => 'admin'], function()
{
    $controller = 'Groupeat\Admin\Html\AdminController';

    Route::get('phpinfo', "$controller@PHPinfo");

    Route::get('db', "$controller@db");

    Route::get('docs', "$controller@docs");
});
