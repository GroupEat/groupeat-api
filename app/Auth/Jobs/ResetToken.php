<?php
namespace Groupeat\Auth\Jobs;

use Groupeat\Support\Jobs\Abstracts\Job;

class ResetToken extends Job
{
    private $email;
    private $password;

    /**
     * @param string $email
     * @param string $password
     */
    public function __construct($email, $password)
    {
        $this->email = $email;
        $this->password = $password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
