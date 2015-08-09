<?php
namespace Groupeat\Customers\Seeders;

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Abstracts\Seeder;
use Groupeat\Support\Values\PhoneNumber;

class CustomersSeeder extends Seeder
{
    protected function makeEntry($id, $max)
    {
        $customer = Customer::create([
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'phoneNumber' => PhoneNumber::from($this->faker->phoneNumber),
        ]);

        UserCredentials::create([
            'user' => $customer,
            'email' => $this->faker->email,
            'password' => $customer->lastName,
            'locale' => 'fr',
        ]);
    }

    protected function insertAdditionalEntries($id)
    {
        $customer = Customer::create([
            'firstName' => 'Groupeat',
            'lastName' => 'User',
            'phoneNumber' => PhoneNumber::from('0605040302'),
        ]);

        UserCredentials::create([
            'user' => $customer,
            'email' => 'groupeat@ensta.fr',
            'password' => 'groupeat',
            'activatedAt' => Carbon::now(),
            'locale' => 'fr',
            // @codingStandardsIgnoreStart
            'token' => 'eyJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2IiwiaXNzIjoiaHR0cDpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwiaWF0IjoiMTQzNTk2MTE1NSIsImV4cCI6IjIwNjY2ODExNTUiLCJuYmYiOiIxNDM1OTYxMTU1IiwianRpIjoiNWE4Y2Y5OThmNmFiNzI1NzAwOWNjYTBmMmVkOTI2NDYifQ.KlVyE_7LRc164GaQo8anxzwtrkIiBl06J_w-IadaABg',
            // @codingStandardsIgnoreEnd
        ]);
    }
}
