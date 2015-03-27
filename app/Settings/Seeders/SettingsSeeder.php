<?php
namespace Groupeat\Settings\Seeders;

use Groupeat\Settings\Entities\Setting;
use Groupeat\Support\Database\Abstracts\Seeder;

class SettingsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        Setting::create([
            'cast' => 'bool',
            'label' => 'notificationsEnabled',
            'default' => true,
        ]);

        Setting::create([
            'cast' => 'int',
            'label' => 'daysWithoutNotifying',
            'default' => 3,
        ]);

        Setting::create([
            'cast' => 'string',
            'label' => 'noNotificationAfter',
            'default' => '22:00:00',
        ]);
    }
}
