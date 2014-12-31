<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Tymon\JWTAuth\JWTAuth;

class GenerateTokenForUser {

    /**
     * @var JWTAuth
     */
    protected $JWTauth;

    /**
     * @var string
     */
    protected $token;


    public function __construct(JWTAuth $JWTauth)
    {
        $this->JWTauth = $JWTauth;
    }

    public function call(User $user)
    {
        $this->token = $this->JWTauth->fromUser($user->Credentials);

        return true;
    }

    public function getToken()
    {
        return $this->token;
    }

}
