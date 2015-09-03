<?php
namespace Groupeat\Customers\Seeders;

use Groupeat\Customers\Entities\Address;
use Groupeat\Support\Database\Abstracts\Seeder;
use Phaza\LaravelPostgis\Geometries\Point;

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
            'location' => new Point($this->faker->latitude, $this->faker->longitude),
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
            'location' => new Point(48.712219, 2.217853),
        ]);
    }
}
