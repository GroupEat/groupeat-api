<?php

use Sly\NotificationPusher\Adapter\Gcm;
use Sly\NotificationPusher\Collection\DeviceCollection;
use Sly\NotificationPusher\Model\Device;
use Sly\NotificationPusher\Model\Message;
use Sly\NotificationPusher\Model\Push;
use Sly\NotificationPusher\PushManager;

Route::get('send-push-notification/{deviceId}', function ($deviceId) {
    $pushManager = new PushManager(PushManager::ENVIRONMENT_DEV);

    $gcmAdapter = new \Sly\NotificationPusher\Adapter\Gcm([
        'apiKey' => 'AIzaSyBjyfcgeWD4UlHxANBs5-6rupcGp0_u1V0',
    ]);

    $devices = new DeviceCollection([new Device($deviceId)]);

    $message = new Message('This is an example.');

    $push = new Push($gcmAdapter, $devices, $message);
    $pushManager->add($push);
    $pushManager->push();

    echo 'Sent to device ID: '.$deviceId;
});
