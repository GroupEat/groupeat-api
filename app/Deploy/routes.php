<?php

Route::group(['prefix' => 'api'], function () {
    Route::get('deploy/opcache/reset', function () {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
    });
});
