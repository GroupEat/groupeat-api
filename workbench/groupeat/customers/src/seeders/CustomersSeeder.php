<?php namespace Groupeat\Customers\Seeders;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Seeder;

class CustomersSeeder extends Seeder {

    protected function makeEntry($id, $max)
    {
        Customer::create([
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
            'phoneNumber' => $this->faker->phoneNumber,
        ]);
    }

    protected function insertAdditionalEntries($id)
    {
        Customer::create([
            'firstName' => 'Jean-Nathanael',
            'lastName' => 'Hérault',
            'phoneNumber' => '0605040302',
        ]);
    }

}
