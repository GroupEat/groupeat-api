<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Exceptions\Unauthorized;
use Tymon\JWTAuth\JWTAuth;

class GenerateAuthToken {

    /**
     * @var JWTAuth
     */
    private $JWTauth;

    /**
     * @var int
     */
    private $defaultDurationInMinutes;


    public function __construct(JWTAuth $JWTauth, $defaultDurationInMinutes)
    {
        $this->JWTauth = $JWTauth;
        $this->defaultDurationInMinutes = $defaultDurationInMinutes;
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

        if ($token === false)
        {
            UserCredentials::throwBadPasswordException();
        }

        $userCredentials = $userCredentials->replaceAuthenticationToken($token);
        $this->JWTauth->login($token);

        return $userCredentials;
    }

    /**
     * @param UserCredentials $userCredentials
     * @param int|null        $durationInMinutes
     *
     * @return string The authentication token
     */
    public function forUser(UserCredentials $userCredentials, $durationInMinutes = null)
    {
        if (!$userCredentials->exists)
        {
            // We need to save the credentials to have the id
            $userCredentials->save();
        }

        if (!is_null($durationInMinutes))
        {
            $this->JWTauth->getProvider()->setTTL($durationInMinutes);
        }

        $token = $this->JWTauth->fromUser($userCredentials);

        if (!is_null($durationInMinutes))
        {
            $this->JWTauth->getProvider()->setTTL($this->defaultDurationInMinutes);
        }

        return $token;
    }

}
