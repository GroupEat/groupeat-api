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
        return $registerUser->call(
            $this->email,
            $this->password,
            $this->locale,
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
