<?php

Route::api(['version' => 'v1'], function () {
    Route::group(['prefix' => 'admin', 'protected' => true], function () {
        Route::get('docs', ['uses' => 'Groupeat\Admin\Api\V1\AdminController@docs']);
    });
});
