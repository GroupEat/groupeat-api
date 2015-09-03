<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Support\Jobs\Abstracts\Job;

class ActivateUser extends Job
{
    private $token;

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
