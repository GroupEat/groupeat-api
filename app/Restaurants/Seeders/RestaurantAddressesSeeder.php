<?php
namespace Groupeat\Restaurants\Seeders;

use Groupeat\Restaurants\Entities\Address;
use Groupeat\Support\Database\Abstracts\Seeder;
use Phaza\LaravelPostgis\Geometries\Point;

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
            'location' => new Point($this->faker->latitude, $this->faker->longitude),
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
                'location' => new Point(48.716941, 2.239171),
            ]);
        }

        foreach ([$id + 3, $id + 4] as $currentId) {
            Address::create([
                'restaurantId' => $currentId,
                'street' => "8 Rue Maurice Berteaux",
                'city' => "Palaiseau",
                'postcode' => 91120,
                'state' => "Essonne",
                'country' => "France",
                'location' => new Point(48.855118, 2.345730),
            ]);
        }
    }
}
