<?php

Route::api(['version' => 'v1'], function()
{
    Route::get('deploy/opcache/reset', function()
    {
        if (function_exists('opcache_reset'))
        {
            opcache_reset();
        }
    });
});
