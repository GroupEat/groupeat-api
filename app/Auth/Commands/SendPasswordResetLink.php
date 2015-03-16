<?php
namespace Groupeat\Auth\Commands;

use Groupeat\Support\Commands\Abstracts\Command;

class SendPasswordResetLink extends Command
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
