<?php

use Codeception\Module\ApiHelper;

class CustomersCest {

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
            $I->seeErrorResponse(403, "E-mail should correspond to a Saclay campus account.");
        }
    }

    public function testThatACustomerCanUnregister(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');
        $id = $I->grabDataFromResponse('id');
        $token = $I->grabDataFromResponse('token');

        $I->sendApiDeleteWithToken($token, "customers/$id");
        $I->seeResponseCodeIs(200);
    }

    public function testThatACustomerCannotAccessAnotherCustomerData(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user1@ensta.fr', 'password');
        $id1 = $I->grabDataFromResponse('id');
        $token = $I->grabDataFromResponse('token');

        $this->sendRegistrationRequest($I, 'user2@ensta.fr', 'password');
        $id2 = $I->grabDataFromResponse('id');

        $I->sendApiGetWithToken($token, "customers/$id2");
        $I->seeErrorResponse(401, "Should be authenticated as customer $id2 instead of $id1.");

        $I->sendApiDeleteWithToken($token, "customers/$id2");
        $I->seeErrorResponse(401, "Should be authenticated as customer $id2 instead of $id1.");
    }

    public function testThatACustomerCanUpdateItsData(ApiTester $I)
    {
        $data = [
            'firstName' => 'First Name',
            'lastName' => 'Last Name',
            'phoneNumber' => '06 06 06 06 06',
            'email' => 'user@ensta.fr',
            'password' => 'password',
        ];

        $I->sendApiPost('customers', $data);
        $id = $I->grabDataFromResponse('id');
        $token = $I->grabDataFromResponse('token');

        $newData['firstName'] = 'New first name';
        $newData['lastName'] = 'New last name';
        $newData['phoneNumber'] = '07 07 07 07 07 08';

        $I->sendApiPatchWithToken($token, "customers/$id", $newData);
        $I->seeResponseCodeIs(200);

        foreach ($newData as $key => $value)
        {
            $I->assertEquals($value, $I->grabDataFromResponse($key));
        }
    }

    private function sendRegistrationRequest(ApiTester $I, $email, $password)
    {
        $I->sendApiPost('customers', compact('email', 'password'));
    }

}
