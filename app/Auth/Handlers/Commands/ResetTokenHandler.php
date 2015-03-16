<?php
namespace Groupeat\Auth\Handlers\Commands;

use Groupeat\Auth\Commands\ResetToken;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Services\GenerateAuthToken;
use Tymon\JWTAuth\JWTAuth;

class ResetTokenHandler
{
    private $generateAuthToken;
    private $jwtAuth;

    public function __construct(GenerateAuthToken $generateAuthToken, JWTAuth $jwtAuth)
    {
        $this->generateAuthToken = $generateAuthToken;
        $this->jwtAuth = $jwtAuth;
    }

    public function handle(ResetToken $command)
    {
        $email = $command->getEmail();
        $password = $command->getPlainPassword();

        $userCredentials = UserCredentials::findByEmailOrFail($email);
        $token = $this->jwtAuth->attempt(compact('email', 'password'));

        if ($token === false) {
            UserCredentials::throwBadPasswordException();
        }

        $userCredentials = $userCredentials->replaceAuthenticationToken($token);

        return $userCredentials;
    }
}
