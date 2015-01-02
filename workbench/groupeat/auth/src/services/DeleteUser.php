<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Auth;
use Groupeat\Auth\Entities\Interfaces\User;
use Groupeat\Support\Exceptions\Forbidden;
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
     */
    public function call(User $user)
    {
        if ($user->credentials->id != $this->auth->user()->id)
        {
            throw new Forbidden("Cannot delete another user.");
        }

        $user->credentials->delete();
    }

}
