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
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOiI2IiwiaXNzIjoiaHR0cDpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwiaWF0IjoiMTQyOTU2MDc3MSIsImV4cCI6IjIwNjAyODA3NzEiLCJuYmYiOiIxNDI5NTYwNzcxIiwianRpIjoiYjRmN2ZhMzQ4ZTI2ZWZlY2I3NTg4M2MzYzE3MGFkZGIifQ.MTZhNzA3YmQ2N2Y2OTVhYjJjNGRjYzVhMzI1NmU0OGFkOGRiZTQwOTI1NjIzYjM4YjkzNWFkNTZlMGJiMWVlZQ',
            // @codingStandardsIgnoreEnd
        ]);
    }
}
