<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Support\Jobs\Abstracts\Job;

class SendPasswordResetLink extends Job
{
    private $email;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
