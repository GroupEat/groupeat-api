<?php
namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Values\TokenDurationInMinutes;
use Tymon\JWTAuth\JWTAuth;

class GenerateToken
{
    private $JWTauth;
    private $defaultDurationInMinutes;

    public function __construct(JWTAuth $JWTauth, TokenDurationInMinutes $defaultTokenDurationInMinutes)
    {
        $this->JWTauth = $JWTauth;
        $this->defaultDurationInMinutes = $defaultTokenDurationInMinutes->value();
    }

    public function call(UserCredentials $userCredentials, int $durationInMinutes = null): string
    {
        if (!is_null($durationInMinutes)) {
            $this->JWTauth->manager()->getPayloadFactory()->setTTL($durationInMinutes);
        }

        $token = $this->JWTauth->fromUser($userCredentials);

        if (!is_null($durationInMinutes)) {
            $this->JWTauth->manager()->getPayloadFactory()->setTTL($this->defaultDurationInMinutes);
        }

        return $token;
    }
}
