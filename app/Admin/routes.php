<?php

Route::group(['prefix' => 'api'], function () {
    Route::group(['prefix' => 'admin', 'middleware' => 'auth'], function () {
        Route::get('docs', ['uses' => 'Groupeat\Admin\Http\V1\AdminController@docs']);
    });
});
