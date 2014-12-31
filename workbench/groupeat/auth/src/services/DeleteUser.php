<?php namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\Interfaces\User;

class DeleteUser {

    public function call(User $user)
    {
        return $user->credentials->delete();
    }

}
