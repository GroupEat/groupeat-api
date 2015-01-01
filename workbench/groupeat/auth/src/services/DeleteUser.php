<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Exceptions\Forbidden;
use Illuminate\Auth\AuthManager as Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class DeleteUser {

    /**
     * @var Auth
     */
    private $auth;


    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function call(User $user)
    {
        if ($user->credentials->id != $this->auth->user()->id)
        {
            throw new Forbidden('Trying to delete different user');
        }

        return $user->credentials->delete();
    }

}
