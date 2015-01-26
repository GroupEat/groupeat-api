<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Illuminate\Auth\Reminders\PasswordBroker;

class ResetPassword {

    /**
     * @var GenerateTokenForUser
     */
    private $authTokenGenerator;

    /**
     * @var PasswordBroker
     */
    private $passwordBroker;


    public function __construct(GenerateAuthToken $authTokenGenerator, PasswordBroker $passwordBroker)
    {
        $this->authTokenGenerator = $authTokenGenerator;
        $this->passwordBroker = $passwordBroker;
    }

    /**
     * @param string $token
     * @param string $email
     * @param string $password
     * @param string $password_confirmation
     */
    public function call($token, $email, $password, $password_confirmation)
    {
        $broker = $this->passwordBroker;
        $credentials = compact('token', 'email', 'password', 'password_confirmation');

        $status = $broker->reset($credentials, function(UserCredentials $userCredentials, $plainPassword)
        {
            $userCredentials->resetPassword($plainPassword, $this->authTokenGenerator->forUser($userCredentials));
        });

        switch ($status)
        {
            case $broker::INVALID_USER:
                throw new NotFound(
                    'noUserForPasswordResetToken',
                    $status
                );

            case $broker::INVALID_PASSWORD:
                throw new BadRequest(
                    'badPassword',
                    $status
                );

            case $broker::INVALID_TOKEN:
                throw new Forbidden(
                    'invalidPasswordResetToken',
                    "Cannot reset password because of invalid token."
                );
        }
    }

}
