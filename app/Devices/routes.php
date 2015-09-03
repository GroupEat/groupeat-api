<?php

use Groupeat\Devices\Http\V1\DevicesController;

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->get('devices/platforms', DevicesController::class.'@platformsIndex');

        $api->group(['prefix' => 'customers/{customer}/devices'], function ($api) {
            $api->post('/', DevicesController::class.'@attach');
            $api->get('/', DevicesController::class.'@index');
        });
    });

    $api->put('devices/{device}', DevicesController::class.'@update');
});
