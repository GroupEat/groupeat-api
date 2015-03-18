<?php
namespace Groupeat\Auth\Commands;

use Groupeat\Support\Commands\Abstracts\Command;

class ChangePassword extends Command
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
