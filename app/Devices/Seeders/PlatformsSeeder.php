<?php
namespace Groupeat\Devices\Seeders;

use Groupeat\Devices\Entities\Platform;
use Groupeat\Support\Database\Abstracts\Seeder;

class PlatformsSeeder extends Seeder
{
    protected function insertAdditionalEntries($id)
    {
        Platform::create(['label' => 'android']);
        Platform::create(['label' => 'ios']);
    }
}
