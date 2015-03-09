<?php
namespace Groupeat\Admin\Seeders;

use Config;
use Groupeat\Admin\Entities\Admin;
use Groupeat\Auth\Entities\UserCredentials;
use Groupeat\Support\Database\Seeder;

class AdminsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        $admin = Admin::create([
            'firstName' => 'GroupEat',
            'lastName' => 'App',
        ]);

        UserCredentials::create([
            'user' => $admin,
            'email' => 'admin@groupeat.fr',
            'password' => Config::get('admin::default_admin_password'),
            'locale' => Config::get('app.available_frontend_locales')[0],
        ]);
    }
}
