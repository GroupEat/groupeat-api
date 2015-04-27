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
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOiI2IiwiaXNzIjoiaHR0cDpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwiaWF0IjoiMTQzMDE2OTg5NSIsImV4cCI6IjIwNjA4ODk4OTUiLCJuYmYiOiIxNDMwMTY5ODk1IiwianRpIjoiYTY3YWQwZjM1NzRhNzc5MmEyOWNiNWJjYTJhOTMzNDcifQ.MzA0MTg4YTJmOTZhNzczOGFmNzAwZGMzMDY4M2FiNmFiNmFhYzNkNTI5MTUxMTUyZTYyM2MyMzY5YmRkNTRjMg',
            // @codingStandardsIgnoreEnd
        ]);
    }
}
