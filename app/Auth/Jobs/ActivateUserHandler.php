<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Jobs\ActivateUser;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\NotFound;

class ActivateUserHandler
{
    public function handle(ActivateUser $job)
    {
        $token = $job->getToken();

        if (empty($token)) {
            throw new BadRequest(
                'missingActivationToken',
                "A valid activation token should be given."
            );
        }

        $model = new UserCredentials;
        $userCredentials = $model->where($model->getTableField('activationToken'), $token)->first();

        if (!$userCredentials) {
            throw new NotFound(
                'noUserForActivationToken',
                "Cannot retrieve user from activation token."
            );
        }

        $userCredentials->activate();
    }
}
