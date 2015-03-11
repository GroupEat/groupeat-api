<?php

use Sly\NotificationPusher\Adapter\Gcm;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;

Route::api(['version' => 'v1'], function () {
    Route::group(['prefix' => 'notifications', 'protected' => true], function () {
        $controller = 'Groupeat\Notifications\Api\V1\NotificationsController';

        Route::post('/', "$controller@saveRegistrationId");

        Route::get('/', "$controller@sendNotification");
    });
});
