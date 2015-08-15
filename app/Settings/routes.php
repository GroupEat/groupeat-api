<?php

use Groupeat\Settings\Http\V1\SettingsController;

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->group(['prefix' => 'customers/{customer}/settings'], function ($api) {
            $api->get('/', SettingsController::class.'@index');
            $api->put('/', SettingsController::class.'@update');
        });
    });
});
