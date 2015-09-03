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
        $I->assertSame(false, $I->grabDataFromResponse('notificationsEnabled'));

        $I->sendApiGetWithToken($token, "customers/$id/settings");
        $I->assertSame(false, $I->grabDataFromResponse('notificationsEnabled'));
    }

    public function testThatACustomerCannotUpdateAnUnexistingSetting(ApiTester $I)
    {
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiPutWithToken($token, "customers/$id/settings", [
            'unexistingLabel' => true
        ]);
        $I->seeErrorResponse(400, 'undefinedCustomerSetting');
    }

    public function testThatACustomerCannotEditTheSettingsOfSomeoneElse(ApiTester $I)
    {
        list(, $strangerId) = $I->sendRegistrationRequest('stranger@ensta.fr');
        list($token, $id) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, "customers/$strangerId/settings");
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');
    }
}
