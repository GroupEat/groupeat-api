<?php namespace Groupeat\Auth\Seeders;

use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Seeder;

class UserCredentialsSeeder extends Seeder {

    protected function makeEntry($id, $max)
    {
        UserCredentials::create([
            'user' => Customer::find($id),
            'email' => $this->faker->email,
            'password' => $this->faker->lastName,
        ]);
    }

    protected function insertAdditionalEntries($id)
    {
        UserCredentials::create([
            'user' => Customer::find($id),
            'email' => 'groupeat@groupeat.fr',
            'password' => 'MRSmaTuer',
        ]);
    }

}
