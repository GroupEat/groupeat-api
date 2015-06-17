<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Jobs\ResetToken;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateToken;
use Tymon\JWTAuth\JWTAuth;

class ResetTokenHandler
{
    private $jwtAuth;
    private $generateToken;

    public function __construct(JWTAuth $jwtAuth, GenerateToken $generateToken)
    {
        $this->jwtAuth = $jwtAuth;
        $this->generateToken = $generateToken;
    }

    /**
     * @param ResetToken $job
     *
     * @return UserCredentials
     */
    public function handle(ResetToken $job)
    {
        $email = $job->getEmail();
        $password = $job->getPassword();

        $userCredentials = UserCredentials::findByEmailOrFail($email);
        $token = $this->jwtAuth->attempt(compact('email', 'password'));

        if ($token === false) {
            UserCredentials::throwBadPasswordException();
        }

        $userCredentials->replaceAuthenticationToken($this->generateToken->call($userCredentials));

        return $userCredentials;
    }
}
