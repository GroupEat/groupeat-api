<?php

class DevicesCest
{
    public function testThatTheOperatingSystemsCanBeListed(ApiTester $I)
    {
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, 'devices/operatingSystems');
        $I->seeResponseCodeIs(200);
    }

    public function testThatADeviceCanBeAttachedToACustomer(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $androidId = $this->getAndroidOsId($I, $token);
        $I->sendApiPostWithToken($token, "customers/$id/devices", [
            'hardwareId' => 'hardwareId',
            'notificationToken' => 'notificationToken',
            'operatingSystemId' => $androidId,
            'operatingSystemVersion' => '5.0.1 Lollipop',
            'model' => 'black 16Go Nexus 5',
            'latitude' => 48.7173,
            'longitude' => 2.23935,
        ]);
        $I->seeResponseCodeIs(201);
    }

    private function getAndroidOsId(ApiTester $I, $token)
    {
        $I->sendApiGetWithToken($token, 'devices/operatingSystems');

        foreach ($I->grabDataFromResponse('') as $os) {
            if ($os['label'] == 'android') {
                return $os['id'];
            }
        }

        $I->fail("Cannot find android OS id.");
    }
}
