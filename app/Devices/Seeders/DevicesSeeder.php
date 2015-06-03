<?php
namespace Groupeat\Devices\Seeders;

use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\Platform;
use Groupeat\Support\Database\Abstracts\Seeder;

class DevicesSeeder extends Seeder
{
    protected function makeEntry($id)
    {
        $device = new Device;
        $device->customerId = $id;
        $device->UUID = uniqid();
        $device->platformId = 2;
        $device->model = "smartphone $id";
        $device->notificationToken = uniqid();
        $device->save();
    }

    protected function insertAdditionalEntries($id)
    {
        $device = new Device;
        $device->customerId = $id;
        $device->UUID = uniqid();
        $device->platformId = 1;
        $device->model = 'NOKIA 3310';
        $device->notificationToken = uniqid();
        $device->save();
    }
}
