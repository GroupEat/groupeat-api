<?php

class SettingsCest
{
    public function testThatTheSettingsOfACustomerCanBeListed(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, "customers/$id/settings");
        $I->seeResponseCodeIs(200);
    }

    public function testThatACustomerCanUpdateItsSettings(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiPutWithToken($token, "customers/$id/settings", [
            'notificationsEnabled' => true
        ]);
        $I->seeResponseCodeIs(200);
        $I->sendApiGetWithToken($token, "customers/$id/settings");
        $I->assertSame(true, $I->grabDataFromResponse('notificationsEnabled'));

        $I->sendApiPutWithToken($token, "customers/$id/settings", [
            'notificationsEnabled' => false
        ]);
        $I->sendApiGetWithToken($token, "customers/$id/settings");
        $I->assertSame(false, $I->grabDataFromResponse('notificationsEnabled'));
    }

    public function testThatACustomerCannotUpadateAnUnexistingSetting(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiPutWithToken($token, "customers/$id/settings", [
            'unexistingSetting' => true
        ]);
        $I->seeErrorResponse(404, 'unexistingLabel');
    }

    public function testThatACustomerCannotEditTheSettingOfSomeoneElse(ApiTester $I)
    {
        list($temp, $strangerId) = $I->sendRegistrationRequest('stranger@ensta.fr');
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, "customers/$strangerId/settings");
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');
    }
}
