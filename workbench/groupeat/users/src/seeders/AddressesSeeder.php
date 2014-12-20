<?php namespace Groupeat\Users\Seeders;

use Groupeat\Users\Entities\User;
use Groupeat\Users\Entities\Address;
use Groupeat\Support\Database\Seeder;

class AddressesSeeder extends Seeder {

    protected function makeEntry($i, $max)
    {
        Address::create([
            'user_id' => $this->faker->numberBetween(1, $max),
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

    protected function insertAdditionalEntries()
    {
        Address::create([
           'user_id' => User::where('email', 'groupeat@groupeat.fr')->first()->id,
           'street' => '828 Boulevard des Maréchaux',
           'details' => 'Bâtiment E, studio 311',
           'city' => 'Palaiseau',
           'postcode' => '91120',
           'state' => 'Essonne',
           'country' => 'France',
           'longitude' => 48.711042,
           'latitude' => 2.219278,
        ]);
    }

}
