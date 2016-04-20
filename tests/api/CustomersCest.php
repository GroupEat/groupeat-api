<?php

class CustomersCest
{
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
            'phoneNumber' => '33606060606',
        ];

        $I->sendApiPutWithToken($token, "customers/$id", $data);
        $I->seeResponseCodeIs(200);

        foreach ($data as $key => $value) {
            $I->assertSame($value, $I->grabDataFromResponse($key));
        }
    }

    public function testThatPhoneNumberWithBadFormatAreRejected(ApiTester $I)
    {
        list($token, $id) = $this->sendRegistrationRequest($I);

        $phoneNumber = '+33605040302';
        $I->sendApiPutWithToken($token, "customers/$id", compact('phoneNumber'));
        $I->seeErrorResponse(400, 'badPhoneNumberFormat');

        $phoneNumber = '06 05 04 03 02';
        $I->sendApiPutWithToken($token, "customers/$id", compact('phoneNumber'));
        $I->seeErrorResponse(400, 'badPhoneNumberFormat');

        $phoneNumber = '0605040302';
        $I->sendApiPutWithToken($token, "customers/$id", compact('phoneNumber'));
        $I->seeErrorResponse(400, 'badPhoneNumberFormat');

        $phoneNumber = '06-05-04-03-02';
        $I->sendApiPutWithToken($token, "customers/$id", compact('phoneNumber'));
        $I->seeErrorResponse(400, 'badPhoneNumberFormat');
    }

    protected function sendRegistrationRequest(
        ApiTester $I,
        string $email = 'user@ensta.fr',
        string $password = 'password',
        string $locale = 'fr'
    ): array {
        return $I->sendRegistrationRequest($email, $password, 'customers', $locale);
    }
}
