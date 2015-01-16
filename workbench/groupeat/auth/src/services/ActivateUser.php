<?php namespace Groupeat\Auth\Services;

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\NotFound;

class ActivateUser {

    /**
     * @param string          $activationToken
     */
    public function call($activationToken)
    {
        $userCredentials = UserCredentials::where('activationToken', $activationToken)->first();

        if (!$userCredentials)
        {
            throw new NotFound("Cannot retrieve user from token.");
        }

        $userCredentials->activationToken = null;
        $userCredentials->activated_at = Carbon::now();

        $userCredentials->save();
    }

}
