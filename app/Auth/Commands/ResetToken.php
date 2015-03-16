<?php
namespace Groupeat\Auth\Commands;

use Groupeat\Support\Commands\Abstracts\Command;

class ResetToken extends Command
{
    private $email;
    private $plainPassword;

    /**
     * @param string $email
     * @param string $plainPassword
     */
    public function __construct($email, $plainPassword)
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }
}
