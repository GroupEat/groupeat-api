<?php

use Groupeat\Notifications\Http\V1\NotificationsController;

$api->version('v1', function ($api) {
    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->post('groupOrders/{groupOrder}/notifications', NotificationsController::class.'@send');
    });
});
