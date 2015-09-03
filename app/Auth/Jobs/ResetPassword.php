<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Support\Jobs\Abstracts\Job;

class ResetPassword extends Job
{
    private $token;
    private $email;
    private $newPassword;

    /**
     * @param string $token
     * @param string $email
     * @param string $newPassword
     */
    public function __construct($token, $email, $newPassword)
    {
        $this->token = $token;
        $this->email = $email;
        $this->newPassword = $newPassword;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }
}
