<?php

class CustomersCest
{
    public function testThatACustomerNeedsACampusEmailToRegister(ApiTester $I)
    {
        foreach (['ensta.fr', 'ensta-paristech.fr', 'institutoptique.fr', 'polytechnique.edu'] as $domain) {
            $this->sendRegistrationRequest($I, "user@$domain");
        }

        foreach (['gmail.com', 'supelec.fr', 'ensta.com', 'ensta.org'] as $domain) {
            $I->sendApiPost('customers', ['email' => "user@$domain", 'password' => 'password', 'locale' => 'fr']);
            $I->seeResponseCodeIs(422);
            $I->seeErrorsContain(['email' => ['notFromCampus' => []]]);
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
        list($token2, $id2) = $this->sendRegistrationRequest($I, 'user2@ensta.fr');

        $I->sendApiGetWithToken($token1, "customers/$id1");
        $I->seeResponseCodeIs(200);

        $I->sendApiGetWithToken($token1, "customers/$id2");
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');

        $I->sendApiDeleteWithToken($token1, "customers/$id2");
        $I->seeErrorResponse(403, 'wrongAuthenticatedUser');
    }

    public function testThatACustomerCanUpdateItsData(ApiTester $I)
    {
        list($token, $id) = $this->sendRegistrationRequest($I);

        $data = [
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'phoneNumber' => '06 06 06 06 06',
        ];

        $I->sendApiPutWithToken($token, "customers/$id", $data);
        $I->seeResponseCodeIs(200);

        foreach ($data as $key => $value) {
            $I->assertSame($value, $I->grabDataFromResponse($key));
        }
    }

    protected function sendRegistrationRequest(
        ApiTester $I,
        $email = 'user@ensta.fr',
        $password = 'password',
        $locale = 'fr'
    ) {
        return $I->sendRegistrationRequest($email, $password, 'customers', $locale);
    }
}
