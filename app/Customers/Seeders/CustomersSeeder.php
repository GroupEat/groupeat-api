<?php
namespace Groupeat\Customers\Seeders;

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Abstracts\Seeder;

class CustomersSeeder extends Seeder
{
    protected function makeEntry($id, $max)
    {
        $customer = Customer::create([
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'phoneNumber' => $this->faker->phoneNumber,
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
            'phoneNumber' => '0605040302',
        ]);

        UserCredentials::create([
            'user' => $customer,
            'email' => 'groupeat@ensta.fr',
            'password' => 'groupeat',
            'activatedAt' => Carbon::now(),
            'locale' => 'fr',
            // @codingStandardsIgnoreStart
            'token' => 'eyJhbGciOiJIUzM4NCIsInR5cCI6IkpXUyJ9.eyJzdWIiOiIzIiwiaXNzIjoiaHR0cDpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwiaWF0IjoiMTQyOTU2NDM2OSIsImV4cCI6IjIwNjAyODQzNjkiLCJuYmYiOiIxNDI5NTY0MzY5IiwianRpIjoiOWIxYmVmYjk0N2YwYWMxZWEyOTNmMzQ1YWJiZDdhYmYifQ.YzczMWRhZjk0ODY3YjJiZjVlNDU1NjM1YmViOTM5NDE4NDRjNmJhZmRlODEzYmFmNjRjNDcxMzljYWE4ZjNmMzNjNTBiYjZjMWRkYmY4NzI1MTM2NzRiZjdjMjdhZGM3',
            // @codingStandardsIgnoreEnd
        ]);
    }
}
