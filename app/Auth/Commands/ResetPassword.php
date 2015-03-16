<?php
namespace Groupeat\Auth\Commands;

use Groupeat\Support\Commands\Abstracts\Command;

class ResetPassword extends Command
{
    private $token;
    private $email;
    private $newPlainPassword;

    /**
     * @param string $token
     * @param string $email
     * @param string $newPlainPassword
     */
    public function __construct($token, $email, $newPlainPassword)
    {
        $this->token = $token;
        $this->email = $email;
        $this->newPlainPassword = $newPlainPassword;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getNewPlainPassword()
    {
        return $this->newPlainPassword;
    }
}
