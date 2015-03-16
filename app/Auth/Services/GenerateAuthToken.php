<?php
namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Auth\Values\TokenDurationInMinutes;
use Tymon\JWTAuth\JWTAuth;

class GenerateAuthToken
{
    private $JWTauth;
    private $defaultDurationInMinutes;

    public function __construct(JWTAuth $JWTauth, TokenDurationInMinutes $defaultTokenDurationInMinutes)
    {
        $this->JWTauth = $JWTauth;
        $this->defaultDurationInMinutes = $defaultTokenDurationInMinutes->value();
    }

    /**
     * @param UserCredentials $userCredentials
     * @param int             $durationInMinutes Null for default duration
     *
     * @return string The authentication token
     */
    public function call(UserCredentials $userCredentials, $durationInMinutes = null)
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
