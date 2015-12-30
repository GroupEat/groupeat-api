<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Support\Jobs\Abstracts\Job;

class ChangePassword extends Job
{
    private $email;
    private $oldPassword;
    private $newPassword;

    public function __construct(string $email, string $oldPassword, string $newPassword)
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
