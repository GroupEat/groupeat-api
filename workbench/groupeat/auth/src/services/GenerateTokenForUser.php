<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Exceptions\Forbidden;
use Groupeat\Support\Exceptions\NotFound;
use Tymon\JWTAuth\JWTAuth;

class GenerateTokenForUser {

    /**
     * @var JWTAuth
     */
    private $JWTauth;

    /**
     * @var Auth
     */
    private $groupeatAuth;


    public function __construct(JWTAuth $JWTauth, Auth $groupeatAuth)
    {
        $this->JWTauth = $JWTauth;
        $this->groupeatAuth = $groupeatAuth;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return User
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
            throw new NotFound("Cannot retrieve user from token.");
        }

        $user->token = $token;
        $user->save();

        $this->groupeatAuth->setUserCredentials($user);

        return $user->user;
    }

}
