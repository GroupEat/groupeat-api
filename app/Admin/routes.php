<?php

use Groupeat\Admin\Http\V1\AdminController;

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
        Route::get('docs', ['uses' => AdminController::class.'@docs']);
    });
});
