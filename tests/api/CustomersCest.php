<?php

class CustomersCest {

    public function testThatACustomerNeedsACampusEmailToRegister(ApiTester $I)
    {
        foreach (['ensta.fr', 'ensta-paristech.fr', 'institutoptique.fr', 'polytechnique.edu'] as $domain)
        {
            $this->sendRegistrationRequest($I,"user@$domain");
        }

        foreach (['gmail.com', 'supelec.fr', 'ensta.com', 'ensta.org'] as $domain)
        {
            $I->sendApiPost('customers', ['email' => "user@$domain", 'password' => 'password']);
            $I->seeErrorResponse(403, "E-mail should correspond to a Saclay campus account.");
        }
    }

    public function testThatACustomerCanUnregister(ApiTester $I)
    {
        list($token, $id) = $this->sendRegistrationRequest($I);

        $I->sendApiDeleteWithToken($token, "customers/$id");
        $I->seeResponseCodeIs(200);
    }

    public function testThatACustomerCannotAccessAnotherCustomerData(ApiTester $I)
    {
        list($token1, $id1) = $this->sendRegistrationRequest($I, 'user1@ensta.fr');
        $id1 = $I->grabDataFromResponse('id');
        $token1 = $I->grabDataFromResponse('token');

        list($token2, $id2) = $this->sendRegistrationRequest($I, 'user2@ensta.fr');

        $I->sendApiGetWithToken($token1, "customers/$id2");
        $I->seeErrorResponse(401, "Should be authenticated as customer $id2 instead of $id1.");

        $I->sendApiDeleteWithToken($token1, "customers/$id2");
        $I->seeErrorResponse(401, "Should be authenticated as customer $id2 instead of $id1.");
    }

    public function testThatACustomerCanUpdateItsData(ApiTester $I)
    {
        list($token, $id) = $this->sendRegistrationRequest($I);

        $data = [
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'phoneNumber' => '06 06 06 06 06',
        ];

        $I->sendApiPatchWithToken($token, "customers/$id", $data);
        $I->seeResponseCodeIs(200);

        foreach ($data as $key => $value)
        {
            $I->assertEquals($value, $I->grabDataFromResponse($key));
        }
    }

    protected function sendRegistrationRequest(
        ApiTester $I,
        $email = 'user@ensta.fr',
        $password = 'password',
        $locale = 'fr'
    )
    {
        return $I->sendRegistrationRequest($email, $password, 'customers', $locale);
    }

}
