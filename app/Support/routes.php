<?php

use Groupeat\Support\Http\V1\SupportController;

// There need to be at least one regular route to be able to use to cache the other API routes.
Route::post('/api/ping', SupportController::class.'@ping');

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('/config', SupportController::class.'@config');
    });
});
