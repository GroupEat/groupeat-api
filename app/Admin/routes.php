<?php

use Groupeat\Admin\Http\V1\AdminController;

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('docs', AdminController::class.'@docs');
        $api->post('devices/{device}/notifications', AdminController::class.'@sendNotification');
    });
});
