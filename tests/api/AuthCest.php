<?php

use Codeception\Module\ApiHelper;

class AuthCest {

    public function testThatPassingATokenInTheQueryStringIsForbidden(ApiTester $I)
    {
        $I->sendApiGET('auth/token?token=shouldBePassedInHeader');
        $I->seeErrorResponse(403, "Trying to authenticate via token in query string is forbidden.");
    }

    public function testThatAUserCanBeRegistered(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');

        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(['type' => 'customer']);
    }

    public function testThatAUserCanAuthenticateAfterSuccessfulRegistration(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');
        $I->seeResponseCodeIs(201);

        $id = $I->grabDataFromJsonResponse('id');
        $token = $I->grabDataFromJsonResponse('token');
        $I->haveAuthenticationToken($token);
        $I->sendApiGET($this->getUserType().'/'.$id);
        $I->seeResponseCodeIs(200);
    }

    public function testThatAUserCanAskForANewTokenInExchangeOfValidCredentials(ApiTester $I)
    {
        $email = 'user@ensta.fr';
        $password = 'password';
        $this->sendRegistrationRequest($I, $email, $password);
        $I->seeResponseCodeIs(201);
        $id = $I->grabDataFromJsonResponse('id');
        $oldToken = $I->grabDataFromJsonResponse('token');

        sleep(1.1);
        $this->sendTokenRefreshRequest($I, $email, $password);
        $I->seeResponseCodeIs(200);

        $newToken = $I->grabDataFromJsonResponse('token');
        $I->assertNotEmpty($newToken);
        $I->assertNotEquals($oldToken, $newToken);

        $I->haveAuthenticationToken($newToken);
        $I->sendApiGET($this->getUserType().'/'.$id);
        $I->seeResponseCodeIs(200);

        $I->haveAuthenticationToken($oldToken);
        $I->sendApiGET($this->getUserType().'/'.$id);
        $I->seeErrorResponse(401, "Obsolete token.");
    }

    public function testThatAUserMustGiveAWellFormattedEmailToRegister(ApiTester $I)
    {
        foreach (['user#gmail@ensta.fr', 'user&user@polytechnique.edu'] as $invalidEmail)
        {
            $this->sendRegistrationRequest($I, "user@$invalidEmail", 'password');
            $I->seeErrorResponse(400, "Invalid credentials.");
            $I->seeErrorsContain(['email' => ["The email must be a valid email address."]]);
        }
    }

    public function testThatAUserCannotRegisterWithAnEmailAlreadyTaken(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'other_password');

        $I->seeErrorResponse(400, "Invalid credentials.");
        $I->seeErrorsContain(['email' => ["The email has already been taken."]]);
    }

    public function testThatThePasswordMustBeAtLeastSixCharacters(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user1@ensta.fr', '123456');
        $I->seeResponseCodeIs(201);

        $this->sendRegistrationRequest($I, 'user2@ensta.fr', '12345');
        $I->seeErrorsContain(['password' => ["The password must be at least 6 characters."]]);
    }

    private function sendTokenRefreshRequest(ApiTester $I, $email, $password)
    {
        $I->sendApiPUT('auth/token', compact('email', 'password'));
    }

    private function sendRegistrationRequest(ApiTester $I, $email, $password)
    {
        $I->sendApiPOST($this->getUserType(), compact('email', 'password'));
    }

    private function getUserType()
    {
        return 'customers'; // Could have been admin or any other user type.
    }

}
