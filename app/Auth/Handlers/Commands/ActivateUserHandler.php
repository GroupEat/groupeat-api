<?php
namespace Groupeat\Auth\Handlers\Commands;

use Groupeat\Auth\Commands\ActivateUser;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\NotFound;

class ActivateUserHandler
{
    public function handle(ActivateUser $command)
    {
        $token = $command->getToken();

        if (empty($token)) {
            throw new BadRequest(
                'missingActivationToken',
                "A valid activation token should be given."
            );
        }

        $userCredentials = UserCredentials::where('activationToken', $token)->first();

        if (!$userCredentials) {
            throw new NotFound(
                'noUserForActivationToken',
                "Cannot retrieve user from activation token."
            );
        }

        $userCredentials->activate();
    }
}
