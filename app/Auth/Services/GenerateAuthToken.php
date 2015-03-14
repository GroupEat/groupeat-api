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
        $this->JWTauth = $JWTauth; // TODO: check if Groupeat Auth can be used instead
        $this->defaultDurationInMinutes = $defaultTokenDurationInMinutes->value();
    }

    /**
     * @param $email
     * @param $password
     *
     * @return UserCredentials
     */
    public function resetFromCredentials($email, $password)
    {
        $userCredentials = UserCredentials::findByEmailOrFail($email);
        $token = $this->JWTauth->attempt(compact('email', 'password'));

        if ($token === false) {
            UserCredentials::throwBadPasswordException();
        }

        $userCredentials = $userCredentials->replaceAuthenticationToken($token);
        $this->JWTauth->authenticate($token);

        return $userCredentials;
    }

    /**
     * @param UserCredentials $userCredentials
     * @param int             $durationInMinutes Null for default duration
     *
     * @return string The authentication token
     */
    public function forUser(UserCredentials $userCredentials, $durationInMinutes = null)
    {
        if (!$userCredentials->exists) {
            // We need to save the credentials to have the id
            $userCredentials->save();
        }

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
