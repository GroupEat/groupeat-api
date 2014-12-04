<?php

use Groupeat\Database\Seeder;

class UsersSeeder extends Seeder {

    const MODEL_CLASS = 'User';

    public function makeEntry()
    {
        User::create([
            'email' => $this->faker->email,
            'firstName' => $this->faker->firstName,
            'lastName' => $this->faker->lastName,
        ]);
    }

}
