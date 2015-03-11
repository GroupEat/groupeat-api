<?php
namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Translation\Translator;

class ResetPassword
{
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
     */
    public function call($token, $email, $password)
    {
        $broker = $this->passwordBroker;
        $password_confirmation = $password;
        $credentials = compact('token', 'email', 'password', 'password_confirmation');

        $status = $broker->reset($credentials, function (UserCredentials $userCredentials, $plainPassword) {
            $userCredentials->resetPassword($plainPassword, $this->authTokenGenerator->forUser($userCredentials));
        });

        switch ($status) {
            case $broker::INVALID_USER:
                throw new NotFound(
                    'noUserForPasswordResetToken',
                    "No user corresponding to this password reset token."
                );

            case $broker::INVALID_PASSWORD:
                throw new UnprocessableEntity(
                    'badPassword',
                    "The password must be at least six characters."
                );

            case $broker::INVALID_TOKEN:
                throw new Forbidden(
                    'invalidPasswordResetToken',
                    "This password reset token is invalid."
                );
        }
    }
}
