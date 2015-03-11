<?php
namespace Groupeat\Customers\Seeders;

use Carbon\Carbon;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Seeder;

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
            'activated_at' => Carbon::now(),
            'locale' => 'fr',
            // @codingStandardsIgnoreStart
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXUyJ9.eyJzdWIiOiI2IiwiaXNzIjoiaHR0cHM6XC9cL2dyb3VwZWF0LmRldlwvYXBpXC9hdXRoXC90b2tlbiIsImlhdCI6IjE0MjYwODIzNzYiLCJleHAiOiIxNDI2MDg1OTc2IiwibmJmIjoiMTQyNjA4MjM3NiIsImp0aSI6IjY0OTZjZTFhODg4MTZmZTBlYmU1ZGJmYzk5NDNiNDU2In0.MTUwODgyZmYyMjk0MWU0NWNmYzg2NTE2ZmJkZTYzOWQ1ZTE3N2Q4ZDZhNmVhZGY4NjRmYmJiMzFlYWJhNTQwZA',
            // @codingStandardsIgnoreEnd
        ]);
    }
}
