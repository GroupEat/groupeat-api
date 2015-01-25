<?php namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\Address;
use Groupeat\Support\Database\Seeder;

class RestaurantAddressesSeeder extends Seeder {

    protected function makeEntry($id, $max)
    {
        Address::create([
            'restaurant_id' => $id,
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
        foreach ([$id, $id + 1, $id + 2] as $currentId)
        {
            Address::create([
                'restaurant_id' => $currentId,
                'street' => "8{$currentId} Rue Maurice Berteaux",
                'city' => "Palaiseau",
                'postcode' => 91120,
                'state' => "Essonne",
                'country' => "France",
                'latitude' => 48.717104,
                'longitude' => 2.239332,
            ]);
        }
    }

}
