<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Jobs\ResetPassword;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Auth\PasswordBroker;

class ResetPasswordHandler
{
    private $passwordBroker;
    private $generateToken;

    public function __construct(PasswordBroker $passwordBroker, GenerateToken $generateToken)
    {
        $this->passwordBroker = $passwordBroker;
        $this->generateToken = $generateToken;
    }

    public function handle(ResetPassword $command)
    {
        $email = $command->getEmail();
        $token = $command->getToken();
        $password = $command->getNewPassword();

        $broker = $this->passwordBroker;
        $password_confirmation = $password;
        $credentials = compact('token', 'email', 'password', 'password_confirmation');
        $user = null;

        $status = $broker->reset(
            $credentials,
            function (UserCredentials $userCredentials, $password) use (&$user) {
                $userCredentials->resetPassword($password, $this->generateToken->call($userCredentials));
                $user = $userCredentials;
            }
        );

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

        return $user;
    }
}
