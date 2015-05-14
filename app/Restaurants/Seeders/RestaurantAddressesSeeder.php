<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\Address;
use Groupeat\Support\Database\Abstracts\Seeder;

class RestaurantAddressesSeeder extends Seeder
{
    protected function makeEntry($id, $max)
    {
        Address::create([
            'restaurantId' => $id,
            'street' => $this->faker->streetAddress,
            'city' => $this->faker->city,
            'postcode' => $this->faker->postcode,
            'state' => $this->faker->departmentName,
            'country' => $this->faker->country,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ]);
    }

    protected function insertAdditionalEntries($id)
    {
        foreach ([$id, $id + 1, $id + 2] as $currentId) {
            Address::create([
                'restaurantId' => $currentId,
                'street' => "8 Rue Maurice Berteaux",
                'city' => "Palaiseau",
                'postcode' => 91120,
                'state' => "Essonne",
                'country' => "France",
                'latitude' => 48.716941,
                'longitude' => 2.239171,
            ]);
        }

        Address::create([
            'restaurantId' => $id + 3,
            'street' => "8 Rue Maurice Berteaux",
            'city' => "Palaiseau",
            'postcode' => 91120,
            'state' => "Essonne",
            'country' => "France",
            'latitude' => 48.855118,
            'longitude' => 2.345730,
        ]);
    }
}
