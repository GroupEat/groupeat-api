<?php
namespace Groupeat\Customers\Services;

use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Exceptions\UnprocessableEntity;

class RegisterCustomer
{
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
        return $this->registerUser->call($email, $plainPassword, $locale, new Customer, function ($credentials) {
            $this->assertEmailFromCampus($credentials['email']);
        });
    }

    private function assertEmailFromCampus($email)
    {
        $domains = 'ensta-paristech\.fr|ensta\.fr|polytechnique\.edu|institutoptique\.fr';

        if (!preg_match("/@($domains)$/", $email)) {
            throw new UnprocessableEntity(
                ['email' => ['notFromCampus' => []]],
                "E-mail should correspond to a Saclay campus account."
            );
        }
    }
}
