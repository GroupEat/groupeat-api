<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Jobs\ChangePassword;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Unauthorized;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Tymon\JWTAuth\JWTAuth;

class ChangePasswordHandler
{
    private $jwtAuth;
    private $generateToken;

    public function __construct(JWTAuth $jwtAuth, GenerateToken $generateToken)
    {
        $this->jwtAuth = $jwtAuth;
        $this->generateToken = $generateToken;
    }

    public function handle(ChangePassword $job)
    {
        $email = $job->getEmail();
        $password = $job->getOldPassword();
        $newPassword = $job->getNewPassword();

        $this->assertLongEnough($newPassword);
        $this->assertDifferent($password, $newPassword);

        $userCredentials = UserCredentials::findByEmailOrFail($email);
        $token = $this->jwtAuth->attempt(compact('email', 'password'));

        if ($token === false) {
            throw new Unauthorized(
                ['oldPassword' => ['invalid' => []]],
                "Cannot authenticate with old password."
            );
        }

        $userCredentials->resetPassword($newPassword, $this->generateToken->call($userCredentials));

        return $userCredentials;
    }

    private function assertDifferent($password, $newPassword)
    {
        if ($password == $newPassword) {
            throw new BadRequest(
                'passwordsMustBeDifferent',
                "The new password cannot be the same as the old one."
            );
        }
    }

    private function assertLongEnough($newPassword)
    {
        if (strlen($newPassword) < 6) {
            throw new UnprocessableEntity(
                'badPassword',
                "The password must be at least six characters."
            );
        }
    }
}
