<?php

class AuthCest
{
    public function testThatPassingATokenInTheQueryStringIsForbidden(ApiTester $I)
    {
        $I->sendApiGet('auth/token?token=shouldBePassedInHeader');
        $I->seeErrorResponse(400, 'authenticationTokenInQueryStringForbidden');
    }

    public function testThatAUserCanBeRegistered(ApiTester $I)
    {
        $this->sendRegistrationRequest($I);

        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(['type' => str_singular($this->getUserResource())]);
    }

    public function testThatAUserCanBeActivatedAfterSuccessfulRegistration(ApiTester $I)
    {
        list($token, $id) = $this->sendRegistrationRequest($I);

        $I->sendApiGetWithToken($token, $this->getUserResource().'/'.$id);
        $I->assertFalse($I->grabDataFromResponse('activated'));

        $activationLink = $I->grabHrefInLinkByIdInLastMail('activation-link');
        $I->assertNotEmpty($activationLink);
        list($temp, $activationToken) = explode("token=", $activationLink);
        $I->sendApiPost('auth/activationTokens', ['token' => $activationToken]);
        $I->seeResponseCodeIs(200);

        $I->sendApiGetWithToken($token, $this->getUserResource().'/'.$id);
        $I->assertTrue($I->grabDataFromResponse('activated'));
    }

    public function testThatAUserCanAuthenticateAfterSuccessfulRegistration(ApiTester $I)
    {
        list($token, $id) = $this->sendRegistrationRequest($I);

        $I->sendApiGetWithToken($token, $this->getUserResource().'/'.$id);
        $I->seeResponseCodeIs(200);
    }

    public function testThatAUserCanResetItsTokenInExchangeOfValidCredentials(ApiTester $I)
    {
        $email = 'user@ensta.fr';
        $password = 'password';
        list($oldToken, $id) = $this->sendRegistrationRequest($I, $email, $password);

        sleep(1.5);
        $I->sendApiPost('auth/token', compact('email', 'password'));
        $I->seeResponseCodeIs(200);
        $newToken = $I->grabDataFromResponse('token');
        $I->assertNotEmpty($newToken);
        $I->assertNotEquals($oldToken, $newToken);
        $I->sendApiGetWithToken($newToken, $this->getUserResource().'/'.$id);
        $I->seeResponseCodeIs(200);
        $I->sendApiGetWithToken($oldToken, $this->getUserResource().'/'.$id);
        $I->seeErrorResponse(403, 'obsoleteAuthenticationToken');
    }

    public function testThatAUserCanAskItsTokenInExchangeOfValidCredentials(ApiTester $I)
    {
        $email = 'user@ensta.fr';
        $password = 'password';
        list($originalToken, $id) = $this->sendRegistrationRequest($I, $email, $password);

        $I->sendApiPut('auth/token', compact('email', 'password'));
        $I->seeResponseCodeIs(200);
        $I->assertEquals($originalToken, $I->grabDataFromResponse('token'));
    }

    public function testThatAUserMustGiveAWellFormattedEmailToRegister(ApiTester $I)
    {
        foreach (['user)gmail@ensta.fr', 'user§user@polytechnique.edu'] as $invalidEmail) {
            $I->sendApiPost($this->getUserResource(), [
                'email' => $invalidEmail,
                'password' => 'password',
                'locale' => 'fr',
            ]);

            $I->seeResponseCodeIs(422);
            $I->seeErrorsContain(['email' => ['email' => []]]);
        }
    }

    public function testThatAUserCannotRegisterWithAnEmailAlreadyTaken(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'one_password');
        $I->sendApiPost($this->getUserResource(), [
            'email' => 'user@ensta.fr',
            'password' => 'another_password',
            'locale' => 'fr',
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeErrorsContain(['email' => ['alreadyTaken' => ['user_credentials']]]);
    }

    public function testThatThePasswordMustBeAtLeastSixCharacters(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user1@ensta.fr', '123456');

        $I->sendApiPost($this->getUserResource(), [
            'email' => 'user2@ensta.fr',
            'password' => '12345',
            'locale' => 'fr',
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeErrorsContain(['password' => ['min' => ['6']]]);
    }

    public function testThatAUserCanResetItsPassword(ApiTester $I)
    {
        $email = 'user@ensta.fr';
        $oldPassword = 'password';
        list($oldToken, $id) = $this->sendRegistrationRequest($I, $email, $oldPassword);

        $I->sendApiDelete('auth/password', compact('email'));
        $I->seeResponseCodeIs(200);
        $link = $I->grabHrefInLinkByIdInLastMail('password-reset-link');
        $I->assertNotEmpty($link);
        list($temp, $resetToken) = explode('token=', $link);

        $newPassword = 'new_password';

        $I->sendApiPost('auth/password', [
            'email' => $email,
            'password' => $newPassword,
            'token' => $resetToken,
        ]);

        $I->sendApiPut('auth/token', [
            'email' => $email,
            'password' => $newPassword,
        ]);
        $I->seeResponseCodeIs(200);
        $newToken = $I->grabDataFromResponse('token');
        $I->assertNotEmpty($newToken);
        //$I->assertNotEquals($oldToken, $newToken); // TODO: make this test pass

        $I->sendApiGetWithToken($newToken, $this->getUserResource().'/'.$id);
        $I->seeResponseCodeIs(200);

        $I->sendApiGetWithToken($oldToken, $this->getUserResource().'/'.$id);
        //$I->seeErrorResponse(403, 'obsoleteAuthenticationToken'); // TODO: make this test pass

        $I->sendApiPut('auth/token', [
            'email' => $email,
            'password' => $oldPassword,
        ]);
        $I->seeResponseCodeIs(401);
        $I->seeErrorsContain(['password' => ['invalid' => []]]);
    }

    protected function sendRegistrationRequest(
        ApiTester $I,
        $email = 'user@ensta.fr',
        $password = 'password',
        $locale = 'fr'
    ) {
        return $I->sendRegistrationRequest($email, $password, $this->getUserResource(), $locale);
    }

    private function getUserResource()
    {
        return 'customers'; // Could have been admin or any other user type.
    }
}
