<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Unauthorized;
use Tymon\JWTAuth\JWTAuth;

class GenerateAuthToken {

    /**
     * @var JWTAuth
     */
    private $JWTauth;


    public function __construct(JWTAuth $JWTauth)
    {
        $this->JWTauth = $JWTauth;
    }

    /**
     * @param $email
     * @param $password
     *
     * @return UserCredentials
     */
    public function resetFromCredentials($email, $password)
    {
        $token = $this->assertValid($this->JWTauth->attempt(compact('email', 'password')));

        $user = $this->JWTauth->toUser($token);

        if (!$user)
        {
            throw new Unauthorized("Bad credentials.");
        }

        $user->token = $token;
        $user->save();

        return $user;
    }

    /**
     * @param UserCredentials $userCredentials
     *
     * @return string The authentication token
     */
    public function forUser(UserCredentials $userCredentials)
    {
        if (!$userCredentials->exists)
        {
            // We need to save the credentials to have the id
            $userCredentials->save();
        }

        return $this->assertValid($this->JWTauth->fromUser($userCredentials));
    }

    /**
     * @param string $token
     *
     * @return string The authentication token
     */
    private function assertValid($token)
    {
        if (!$token)
        {
            throw new Forbidden("Bad credentials.");
        }

        return $token;
    }

}
