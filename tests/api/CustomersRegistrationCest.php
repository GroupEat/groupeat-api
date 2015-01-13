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

    private function sendRegistrationRequest(ApiTester $I, $email, $password)
    {
        $I->sendApiPOST('customers', compact('email', 'password'));
    }

}
