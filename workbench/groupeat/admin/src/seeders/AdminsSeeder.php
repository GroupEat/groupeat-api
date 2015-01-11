<?php namespace Groupeat\Admin\Seeders;

use Config;
use Groupeat\Admin\Entities\Admin;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Database\Seeder;

class AdminsSeeder extends Seeder {

    protected function insertAdditionalEntries($id)
    {
        $admin = Admin::create([
            'firstName' => 'GroupEat',
            'lastName' => 'App',
        ]);

        $credentials = Config::get('admin::default_account_credentials');

        UserCredentials::create([
            'user' => $admin,
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);
    }

}
