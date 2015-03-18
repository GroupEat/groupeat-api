<?php

class AdminCest
{
    public function testThatOnlyAdminsCanSeeTheDocs(ApiTester $I)
    {
        $docsUrl = 'admin/docs';
        list($token) = $I->amAnActivatedCustomer();

        $I->sendApiGetWithToken($token, $docsUrl);
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');

        $I->sendApiPost('auth/token', ['email' => 'admin@groupeat.fr', 'password' => 'groupeat']);
        $adminToken = $I->grabDataFromResponse('token');

        $I->sendApiGetWithToken($adminToken, $docsUrl);
        $I->seeResponseCodeIs(200);
        $I->seeResponseContains("GroupEat RESTful API");
    }
}
