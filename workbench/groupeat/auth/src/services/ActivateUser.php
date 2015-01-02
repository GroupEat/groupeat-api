<?php namespace Groupeat\Auth\Services;

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Forbidden;

class ActivateUser {

    /**
     * @param UserCredentials $userCredentials
     * @param string          $activationCode
     */
    public function call($activationCode)
    {
        $userCredentials = UserCredentials::where('activationCode', $activationCode)->first();

        if (!$userCredentials)
        {
            throw new Forbidden("Wrong activation code.");
        }

        if ($userCredentials->activated_at)
        {
            throw new BadRequest("Already activated.");
        }

        $userCredentials->activationCode = null;
        $userCredentials->activated_at = Carbon::now();

        $userCredentials->save();
    }

}
