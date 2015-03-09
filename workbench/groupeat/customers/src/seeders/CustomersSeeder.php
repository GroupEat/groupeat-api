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
            'token' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwczpcL1wvZ3JvdXBlYXQuZGV2XC9hcGlcL2F1dGhcL3Rva2VuIiwic3ViIjo2LCJpYXQiOjE0MjI0NjcwMDUsImV4cCI6MjA1MzE4NzAwNX0.eu26it7Rxlm8hNxJsmeeyquhWqO9R9PV4c_u4pFI5pw',
            // @codingStandardsIgnoreEnd
        ]);
    }
}
