<?php

use Groupeat\Admin\Http\V1\AdminController;

$api->version('v1', function ($api) {
    $api->group(['prefix' => 'admin', 'middleware' => 'api.auth'], function ($api) {
        $api->get('docs', AdminController::class.'@docs');
    });
});
