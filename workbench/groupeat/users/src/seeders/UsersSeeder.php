<?php namespace Groupeat\Users\Seeders;

use Groupeat\Users\Entities\User;
use Groupeat\Support\Database\Seeder;

class UsersSeeder extends Seeder {

    protected function makeEntry($i, $max)
    {
        User::create([
            'email' => $this->faker->email,
            'password' => $this->faker->lastName,
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
        ]);
    }

    protected function insertAdditionalEntries()
    {
        User::create([
            'email' => 'groupeat@groupeat.fr',
            'password' => 'MRSmaTuer',
            'firstName' => 'groupeat',
            'lastName' => 'App',
        ]);
    }

}
