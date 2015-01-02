<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\Forbidden;
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
     * @param string $email
     * @param string $password
     *
     * @return string Authentication token
     */
    public function call($email, $password)
    {
        $token = $this->JWTauth->attempt(compact('email', 'password'));

        if (!$token)
        {
            throw new Forbidden("Bad credentials.");
        }

        $user = $this->JWTauth->toUser($token);

        if (!$user)
        {
            throw new Exception("Cannot retrieve user from token");
        }

        $user->token = $token;
        $user->save();

        return $token;
    }

}
