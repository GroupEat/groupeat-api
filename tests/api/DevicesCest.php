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
        $model = 'black 16Go Nexus 5';

        $I->sendApiPostWithToken($token, "customers/$id/devices", [
            'UUID' => uniqid(),
            'notificationToken' => 'notificationToken',
            'platform' => 'android',
            'version' => '5.0.1 Lollipop',
            'model' => $model,
            'latitude' => 48.716941,
            'longitude' => 2.239171,
        ]);

        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsData(['model' => $model]);

        $I->sendApiGet("customers/$id/devices");
        $I->seeResponseCodeIs(200);
        $I->seeResponseContainsData(['model' => $model]);
    }

    public function testThatADeviceOwnerCanBeChanged(ApiTester $I)
    {
        list($token1, $id1) = $I->amAnActivatedCustomer();
        list($token2, $id2) = $I->amAnActivatedCustomer();
        $deviceId = uniqid();
        $model = 'black 16Go Nexus 5';

        $data = [
            'UUID' => $deviceId,
            'notificationToken' => 'notificationToken',
            'platform' => 'android',
            'version' => '5.0.1 Lollipop',
            'model' => $model,
            'latitude' => 48.716941,
            'longitude' => 2.239171,
        ];

        $I->sendApiPostWithToken($token1, "customers/$id1/devices", $data);

        $I->sendApiGetWithToken($token1, "customers/$id1/devices");
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains($model);

        $I->sendApiGetWithToken($token2, "customers/$id2/devices");
        $I->seeResponseCodeIs(200);
        $I->dontSeeResponseContains($model);

        $I->sendApiPostWithToken($token2, "customers/$id2/devices", $data);

        $I->sendApiGetWithToken($token1, "customers/$id1/devices");
        $I->seeResponseCodeIs(200);
        $I->dontSeeResponseContains($model);

        $I->sendApiGetWithToken($token2, "customers/$id2/devices");
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains($model);

        $I->sendApiGetWithToken($token1, "customers/$id1");
        $email = $I->grabDataFromResponse('email');
        $password = 'password';

        $I->haveHttpHeader('X-Device-Id', $deviceId);
        $I->sendApiPut('auth/token', compact('email', 'password'));

        $I->sendApiGetWithToken($token1, "customers/$id1/devices");
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains($model);

        $I->sendApiGetWithToken($token2, "customers/$id2/devices");
        $I->seeResponseCodeIs(200);
        $I->dontSeeResponseContains($model);
    }
}
