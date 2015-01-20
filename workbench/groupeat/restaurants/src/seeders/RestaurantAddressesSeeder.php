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
            'longitude' => $this->faker->longitude,
            'latitude' => $this->faker->latitude,
        ]);
    }

    protected function insertAdditionalEntries($id)
    {
        Address::create([
           'restaurant_id' => $id,
           'street' => '84 Rue Maurice Berteaux',
           'city' => 'Palaiseau',
           'postcode' => 91120,
           'state' => 'Essonne',
           'country' => 'France',
           'longitude' => 48.717104,
           'latitude' => 2.239332,
        ]);
    }

}
