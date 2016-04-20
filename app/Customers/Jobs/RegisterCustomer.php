<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Auth\Services\RegisterUser;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Jobs\Abstracts\Job;

class RegisterCustomer extends Job
{
    public $email;
    public $password;
    public $locale;

    public function __construct(string $email, string $password, string $locale)
    {
        $this->email = $email;
        $this->password = $password;
        $this->locale = $locale;
    }

    public function handle(RegisterUser $registerUser)
    {
        $customer = $registerUser->call($this->email, $this->password, $this->locale, new Customer);

        // There is no more activation email, the customers are activated by default for now.
        $customer->credentials->activate();

        return $customer;
    }
}
