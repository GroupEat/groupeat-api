<?php

class DevicesCest
{
    public function testThatThePlatformsCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, 'devices/platforms');
        $I->seeResponseCodeIs(200);
    }

    public function testThatADeviceCanBeAttachedToACustomer(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiPostWithToken($token, "customers/$id/devices", [
            'UUID' => uniqid(),
            'notificationToken' => 'notificationToken',
            'platform' => 'android',
            'version' => '5.0.1 Lollipop',
            'model' => 'black 16Go Nexus 5',
            'latitude' => 48.716941,
            'longitude' => 2.239171,
        ]);
        $I->seeResponseCodeIs(201);
    }
}
