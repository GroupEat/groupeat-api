<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Support\Jobs\Abstracts\Job;

class RegisterCustomer extends Job
{
    private $email;
    private $password;
    private $locale;

    /**
     * @param string $email
     * @param string $password
     * @param string $locale
     */
    public function __construct($email, $password, $locale)
    {
        $this->email = $email;
        $this->password = $password;
        $this->locale = $locale;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
