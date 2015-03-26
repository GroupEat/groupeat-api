<?php

class SettingsCest
{
    public function testThatTheSettingsOfACustomerCanBeListed(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, "customers/$id/settings");
        $I->seeResponseCodeIs(200);
    }
}
