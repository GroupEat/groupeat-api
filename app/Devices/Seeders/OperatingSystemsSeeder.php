<?php
namespace Groupeat\Devices\Seeders;

use Groupeat\Devices\Entities\OperatingSystem;
use Groupeat\Support\Database\Abstracts\Seeder;

class OperatingSystemsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        OperatingSystem::create(['label' => 'android']);
        OperatingSystem::create(['label' => 'ios']);
    }
}
