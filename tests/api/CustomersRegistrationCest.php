<?php

use Codeception\Module\ApiHelper;

class CustomersRegistrationCest {

    public function testThatACustomerNeedsACampusEmailToRegister(ApiTester $I)
    {
        foreach (['ensta.fr', 'ensta-paristech.fr', 'institutoptique.fr', 'polytechnique.edu'] as $domain)
        {
            $this->sendRegistrationRequest($I, "user@$domain", 'password');
            $I->seeResponseCodeIs(201);
        }

        foreach (['gmail.com', 'supelec.fr', 'ensta.com', 'ensta.org'] as $domain)
        {
            $this->sendRegistrationRequest($I, "user@$domain", 'password');
            $I->seeErrorResponse(403, "Email should correspond to a Saclay campus account.");
        }
    }

    public function testThatACustomerCanUnregister(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');
        $id = $I->grabDataFromJsonResponse('id');
        $token = $I->grabDataFromJsonResponse('token');

        $I->sendApiDeleteWithToken($token, "customers/$id");
        $I->seeResponseCodeIs(200);
    }

    private function sendRegistrationRequest(ApiTester $I, $email, $password)
    {
        $I->sendApiPost('customers', compact('email', 'password'));
    }

}
