<?php namespace Groupeat\Auth\Seeders;

use Config;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Database\Seeder;

class UserCredentialsSeeder extends Seeder {

    private $locale;

    public function __construct()
    {
        parent::__construct();

        $this->locale = Config::get('app.available_frontend_locales')[0];
    }

    protected function makeEntry($id, $max)
    {
        UserCredentials::create([
            'user' => Customer::find($id),
            'email' => $this->faker->email,
            'password' => $this->faker->lastName,
            'locale' => $this->locale,
        ]);
    }

    protected function insertAdditionalEntries($id)
    {
        UserCredentials::create([
            'user' => Customer::find($id),
            'email' => 'groupeat@groupeat.fr',
            'password' => 'MRSmaTuer',
            'locale' => $this->locale,
        ]);
    }

}
