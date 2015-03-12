<?php
namespace Groupeat\Auth\Services;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\NotFound;

class ActivateUser
{
    /**
     * @param string $activationToken
     */
    public function call($activationToken)
    {
        if (empty($activationToken)) {
            throw new BadRequest(
                "missingActivationToken",
                "A valid activation token should be given."
            );
        }

        $userCredentials = UserCredentials::where('activationToken', $activationToken)->first();

        if (!$userCredentials) {
            throw new NotFound(
                "noUserForActivationToken",
                "Cannot retrieve user from activation token."
            );
        }

        $userCredentials->activate()->save();
    }
}
