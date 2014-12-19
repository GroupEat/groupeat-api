<?php namespace Groupeat\Users\Seeders;

use Groupeat\Users\Models\User;
use Groupeat\Core\Support\Database\Seeder;

class UsersSeeder extends Seeder {

    protected function getModel()
    {
        return new User;
    }

    protected function makeEntry()
    {
        User::create([
            'email' => $this->faker->email,
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
        ]);
    }

    protected function insertAdditionalEntries()
    {

    }
}
