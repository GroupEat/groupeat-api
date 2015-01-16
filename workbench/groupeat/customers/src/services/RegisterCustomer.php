<?php namespace Groupeat\Customers\Services;

use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Exceptions\Forbidden;

class RegisterCustomer {

    /**
     * @var RegisterUser
     */
    private $registerUser;


    public function __construct(RegisterUser $registerUser)
    {
        $this->registerUser = $registerUser;
    }

    /**
     * @param string   $email
     * @param string   $plainPassword
     *
     * @return Customer
     */
    public function call($email, $plainPassword)
    {
        $this->assertCampusEmail($email);

        return $this->registerUser->call($email, $plainPassword, new Customer);
    }

    private function assertCampusEmail($email)
    {
        $domains = ['ensta-paristech.fr', 'ensta.fr', 'polytechnique.edu', 'institutoptique.fr'];

        preg_match('/@([^@]+)$/', $email, $matches);

        if (!empty($matches[1]))
        {
            $domain = $matches[1];

            if (in_array($domain, $domains))
            {
                return $this;
            }
        }

        throw new Forbidden("E-mail should correspond to a Saclay campus account.");
    }

}
