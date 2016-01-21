<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Groupeat\Support\Jobs\Abstracts\Job;
use Tymon\JWTAuth\JWTAuth;

class ResetToken extends Job
{
    private $email;
    private $password;

    public function __construct(string $email, string $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function handle(JWTAuth $jwtAuth, GenerateToken $generateToken): UserCredentials
    {
        $email = $this->email;
        $password = $this->password;

        $userCredentials = UserCredentials::findByEmailOrFail($email);
        $token = $jwtAuth->attempt(compact('email', 'password'));

        if ($token === false) {
            UserCredentials::throwBadPasswordException();
        }

        $userCredentials->replaceAuthenticationToken($generateToken->call($userCredentials));

        return $userCredentials;
    }
}
