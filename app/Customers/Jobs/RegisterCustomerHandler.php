<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Customers\Jobs\RegisterCustomer;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Exceptions\UnprocessableEntity;

class RegisterCustomerHandler
{
    private $registerUser;

    public function __construct(RegisterUser $registerUser)
    {
        $this->registerUser = $registerUser;
    }

    public function handle(RegisterCustomer $command)
    {
        return $this->registerUser->call(
            $command->getEmail(),
            $command->getPassword(),
            $command->getLocale(),
            new Customer,
            function ($credentials) {
                $this->assertEmailFromCampus($credentials['email']);
            }
        );
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
