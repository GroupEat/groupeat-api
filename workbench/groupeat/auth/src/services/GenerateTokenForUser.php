<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Tymon\JWTAuth\JWTAuth;

class GenerateTokenForUser {

    /**
     * @var JWTAuth
     */
    protected $JWTauth;


    public function __construct(JWTAuth $JWTauth)
    {
        $this->JWTauth = $JWTauth;
    }

    /**
     * @param User $user
     *
     * @return string The authentication token
     */
    public function call(UserCredentials $credentials)
    {
        return $this->JWTauth->fromUser($credentials);
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return string The authentication token
     */
    public function callFromCredentials($email, $password)
    {
        return $this->JWTauth->attempt(compact('email', 'password'));
    }

}
