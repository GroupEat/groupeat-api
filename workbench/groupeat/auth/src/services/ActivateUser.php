<?php namespace Groupeat\Auth\Services;

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\Forbidden;
use Symfony\Component\HttpFoundation\Response;

class ActivateUser {

    /**
     * @param UserCredentials $userCredentials
     * @param string          $activationCode
     *
     * @return bool
     */
    public function call(UserCredentials $userCredentials, $activationCode)
    {
        if ($userCredentials->activationCode != $activationCode)
        {
            throw new Forbidden('Wrong activation code');
        }

        if ($userCredentials->activated_at)
        {
            throw new BadRequest('Already activated');
        }

        $userCredentials->activationCode = null;
        $userCredentials->activated_at = Carbon::now();

        return $userCredentials->save();
    }

}
