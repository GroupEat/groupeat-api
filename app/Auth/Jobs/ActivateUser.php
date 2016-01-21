<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Exceptions\BadRequest;
use Groupeat\Support\Exceptions\NotFound;
use Groupeat\Support\Jobs\Abstracts\Job;

class ActivateUser extends Job
{
    private $token;

    public function __construct(string $token)
    {
        $this->token = $token;
    }

    public function handle()
    {
        if (empty($this->token)) {
            throw new BadRequest(
                'missingActivationToken',
                "A valid activation token should be given."
            );
        }

        $model = new UserCredentials;
        $userCredentials = $model->where($model->getTableField('activationToken'), $this->token)->first();

        if (!$userCredentials) {
            throw new NotFound(
                'noUserForActivationToken',
                "Cannot retrieve user from activation token."
            );
        }

        $userCredentials->activate();
    }
}
