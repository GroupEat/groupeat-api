<?php

use Codeception\Module\ApiHelper;
use Groupeat\Customers\Entities\Customer;

class AuthCest {

    public function testThatPassingATokenInTheQueryStringIsForbidden(ApiTester $I)
    {
        $I->sendApiGet('auth/token?token=shouldBePassedInHeader');
        $I->seeErrorResponse(403, "Trying to authenticate via token in query string is forbidden.");
    }

    public function testThatAUserCanBeRegistered(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');

        $I->seeResponseCodeIs(201);
        $I->seeResponseContainsJson(['type' => str_singular($this->getUserType())]);
    }

    public function testThatAUserCanBeActivatedAfterSuccessfulRegistration(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');
        $id = $I->grabDataFromResponse('id');
        $token = $I->grabDataFromResponse('token');

        $I->sendApiGetWithToken($token, $this->getUserType().'/'.$id);
        $I->assertFalse($I->grabDataFromResponse('activated'));

        $activationLink = $I->grabLastMailCrawlableBody()->filter('#activation-link')->text();
        $I->assertNotEmpty($activationLink);
        $I->sendGET($activationLink);
        $I->seeResponseCodeIs(200);

        $I->sendApiGetWithToken($token, $this->getUserType().'/'.$id);
        $I->assertTrue($I->grabDataFromResponse('activated'));
    }

    public function testThatAUserCanAuthenticateAfterSuccessfulRegistration(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');
        $I->seeResponseCodeIs(201);

        $id = $I->grabDataFromResponse('id');
        $token = $I->grabDataFromResponse('token');
        $I->sendApiGetWithToken($token, $this->getUserType().'/'.$id);
        $I->seeResponseCodeIs(200);
    }

    public function testThatAUserCanAskForANewTokenInExchangeOfValidCredentials(ApiTester $I)
    {
        $email = 'user@ensta.fr';
        $password = 'password';
        $this->sendRegistrationRequest($I, $email, $password);
        $I->seeResponseCodeIs(201);
        $id = $I->grabDataFromResponse('id');
        $oldToken = $I->grabDataFromResponse('token');

        sleep(1.1);
        $I->sendApiPost('auth/token', compact('email', 'password'));
        $I->seeResponseCodeIs(200);

        $newToken = $I->grabDataFromResponse('token');
        $I->assertNotEmpty($newToken);
        $I->assertNotEquals($oldToken, $newToken);

        $I->sendApiGetWithToken($newToken, $this->getUserType().'/'.$id);
        $I->seeResponseCodeIs(200);

        $I->sendApiGetWithToken($oldToken, $this->getUserType().'/'.$id);
        $I->seeErrorResponse(401, "Obsolete token.");
    }

    public function testThatAUserMustGiveAWellFormattedEmailToRegister(ApiTester $I)
    {
        foreach (['user#gmail@ensta.fr', 'user&user@polytechnique.edu'] as $invalidEmail)
        {
            $this->sendRegistrationRequest($I, "user@$invalidEmail", 'password');
            $I->seeErrorResponse(400, "Invalid credentials.");
            $I->seeErrorsContain(['email' => ["The e-mail must be a valid e-mail address."]]);
        }
    }

    public function testThatAUserCannotRegisterWithAnEmailAlreadyTaken(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'password');
        $this->sendRegistrationRequest($I, 'user@ensta.fr', 'other_password');

        $I->seeErrorResponse(400, "Invalid credentials.");
        $I->seeErrorsContain(['email' => ["The e-mail has already been taken."]]);
    }

    public function testThatThePasswordMustBeAtLeastSixCharacters(ApiTester $I)
    {
        $this->sendRegistrationRequest($I, 'user1@ensta.fr', '123456');
        $I->seeResponseCodeIs(201);

        $this->sendRegistrationRequest($I, 'user2@ensta.fr', '12345');
        $I->seeErrorsContain(['password' => ["The password must be at least 6 characters."]]);
    }

    public function testThatAUserCanResetItsPassword(ApiTester $I)
    {
        $email = 'user@ensta.fr';
        $oldPassword = 'password';
        $this->sendRegistrationRequest($I, $email, $oldPassword);

        $I->sendApiPost('auth/reset-password', compact('email'));
        $I->seeResponseCodeIs(200);
        $link = $I->grabLastMailCrawlableBody()->filter('#reset-password-link')->text();
        $I->assertNotEmpty($link);
        $I->sendGET($link);
        $I->seeResponseCodeIs(200);

        $newPassword = 'new_password';
        $I->sendPOST($link, [
            'email' => $email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
        ]);
        $I->seeResponseCodeIs(200);

        $I->sendApiPost('auth/token', [
            'email' => $email,
            'password' => $newPassword,
        ]);
        $I->seeResponseCodeIs(200);

        $I->sendApiPost('auth/token', [
            'email' => $email,
            'password' => $oldPassword,
        ]);
        $I->seeErrorResponse(403, "Bad credentials.");
    }

    private function sendRegistrationRequest(ApiTester $I, $email, $password)
    {
        $I->sendApiPost($this->getUserType(), compact('email', 'password'));
    }

    private function getUserType()
    {
        return 'customers'; // Could have been admin or any other user type.
    }

}
