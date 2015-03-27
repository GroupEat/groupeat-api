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
            'street' => "828 Boulevard des Maréchaux",
            'details' => "Bâtiment E, studio 311",
            'city' => "Palaiseau",
            'postcode' => 91120,
            'state' => "Essonne",
            'country' => "France",
            'latitude' => 48.711042,
            'longitude' => 2.219278,
        ]);
    }
}
