<?php
namespace Groupeat\Customers\Commands;

use Groupeat\Support\Commands\Abstracts\Command;

class RegisterCustomer extends Command
{
    private $email;
    private $plainPassword;
    private $locale;

    /**
     * @param string $email
     * @param string $plainPassword
     * @param string $locale
     */
    public function __construct($email, $plainPassword, $locale)
    {
        $this->email = $email;
        $this->plainPassword = $plainPassword;
        $this->locale = $locale;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function getLocale()
    {
        return $this->locale;
    }
}
