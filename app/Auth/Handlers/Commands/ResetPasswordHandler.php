<?php
namespace Groupeat\Auth\Handlers\Commands;

use Groupeat\Auth\Commands\ResetPassword;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateAuthToken;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Auth\PasswordBroker;

class ResetPasswordHandler
{
    private $generateAutToken;
    private $passwordBroker;

    public function __construct(GenerateAuthToken $generateAutToken, PasswordBroker $passwordBroker)
    {
        $this->generateAutToken = $generateAutToken;
        $this->passwordBroker = $passwordBroker;
    }

    public function handle(ResetPassword $command)
    {
        $email = $command->getEmail();
        $token = $command->getToken();
        $password = $command->getNewPlainPassword();

        $broker = $this->passwordBroker;
        $password_confirmation = $password;
        $credentials = compact('token', 'email', 'password', 'password_confirmation');

        $status = $broker->reset($credentials, function (UserCredentials $userCredentials, $plainPassword) {
            $userCredentials->resetPassword($plainPassword, $this->generateAutToken->call($userCredentials));
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
