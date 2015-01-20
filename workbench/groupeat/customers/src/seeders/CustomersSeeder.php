<?php namespace Groupeat\Customers\Seeders;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Seeder;

class CustomersSeeder extends Seeder {

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
            'firstName' => 'Jean-Nathanael',
            'lastName' => 'Hérault',
            'phoneNumber' => '0605040302',
        ]);

        UserCredentials::create([
            'user' => $customer,
            'email' => 'groupeat@groupeat.fr',
            'password' => 'MRSmaTuer',
            'locale' => 'fr',
        ]);
    }

}
