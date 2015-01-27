<?php namespace Groupeat\Customers\Services;

use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Exceptions\UnprocessableEntity;

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
     * @param string $email
     * @param string $plainPassword
     * @param string $locale
     *
     * @return Customer
     */
    public function call($email, $plainPassword, $locale)
    {
        $this->assertCampusEmail($email);

        return $this->registerUser->call($email, $plainPassword, $locale, new Customer);
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
                return true;
            }
        }

        throw new UnprocessableEntity(
            ['email' => ['notFromCampus' => []]],
            "E-mail should correspond to a Saclay campus account."
        );
    }

}
