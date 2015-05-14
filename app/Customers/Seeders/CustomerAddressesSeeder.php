<?php
namespace Groupeat\Customers\Seeders;

use Groupeat\Customers\Entities\Address;
use Groupeat\Support\Database\Abstracts\Seeder;

class CustomerAddressesSeeder extends Seeder
{
    protected function makeEntry($id, $max)
    {
        Address::create([
            'customerId' => $id,
            'street' => $this->faker->streetAddress,
            'details' => $this->faker->buildingNumber,
            'city' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'state' => $this->faker->departmentName,
            'country' => $this->faker->country,
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
        ]);
    }

    protected function insertAdditionalEntries($id)
    {
        Address::create([
            'customerId' => $id,
            'street' => "Sainte Chapelle",
            'details' => "Au niveau du clocher",
            'city' => "Panam, First borough",
            'postcode' => 91120,
            'state' => "IdF",
            'country' => "France",
            'latitude' => 48.712219,
            'longitude' => 2.217853,
        ]);
    }
}
