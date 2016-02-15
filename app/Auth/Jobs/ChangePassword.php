<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Unauthorized;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Jobs\Abstracts\Job;
use Tymon\JWTAuth\JWTAuth;

class ChangePassword extends Job
{
    private $email;
    private $oldPassword;
    private $newPassword;

    public function __construct(string $email, string $oldPassword, string $newPassword)
    {
        $this->email = $email;
        $this->oldPassword = $oldPassword;
        $this->newPassword = $newPassword;
    }

    public function handle(JWTAuth $jwtAuth, GenerateToken $generateToken)
    {
        $this->assertLongEnough($this->newPassword);
        $this->assertDifferent($this->oldPassword, $this->newPassword);

        $userCredentials = UserCredentials::findByEmailOrFail($this->email);
        $token = $jwtAuth->attempt(['email' => $this->email, 'password' => $this->oldPassword]);

        if ($token === false) {
            throw new Unauthorized(
                ['oldPassword' => ['invalid' => []]],
                "Cannot authenticate with old password."
            );
        }

        $userCredentials->resetPassword($this->newPassword, $generateToken->call($userCredentials));

        return $userCredentials;
    }

    private function assertLongEnough($password)
    {
        if (strlen($password) < 6) {
            throw new UnprocessableEntity(
                'badPassword',
                "The password must be at least six characters."
            );
        }
    }

    private function assertDifferent($oldPassword, $newPassword)
    {
        if ($oldPassword == $newPassword) {
            throw new BadRequest(
                'passwordsMustBeDifferent',
                "The new password cannot be the same than the old one."
            );
        }
    }
}
