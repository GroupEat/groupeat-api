<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Support\Jobs\Abstracts\Job;

class ChangePassword extends Job
{
    private $email;
    private $oldPassword;
    private $newPassword;

    /**
     * @param string $email
     * @param string $oldPassword
     * @param string $newPassword
     */
    public function __construct($email, $oldPassword, $newPassword)
    {
        $this->email = $email;
        $this->oldPassword = $oldPassword;
        $this->newPassword = $newPassword;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getOldPassword()
    {
        return $this->oldPassword;
    }

    public function getNewPassword()
    {
        return $this->newPassword;
    }
}
