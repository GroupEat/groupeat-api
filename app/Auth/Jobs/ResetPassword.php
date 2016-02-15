<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Auth\PasswordBroker;

class ResetPassword extends Job
{
    private $token;
    private $email;
    private $newPassword;

    public function __construct(string $token, string $email, string $newPassword)
    {
        $this->token = $token;
        $this->email = $email;
        $this->newPassword = $newPassword;
    }

    public function handle(PasswordBroker $broker, GenerateToken $generateToken)
    {
        $email = $this->email;
        $token = $this->token;
        $password = $this->newPassword;

        $password_confirmation = $password;
        $credentials = compact('token', 'email', 'password', 'password_confirmation');
        $user = null;

        $status = $broker->reset(
            $credentials,
            function (UserCredentials $userCredentials, $password) use (&$user, $generateToken) {
                $userCredentials->resetPassword($password, $generateToken->call($userCredentials));
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
