<?php
namespace Groupeat\Devices\Seeders;

use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\Platform;
use Groupeat\Support\Database\Abstracts\Seeder;
use Phaza\LaravelPostgis\Geometries\Point;

class DevicesSeeder extends Seeder
{
    protected function makeEntry($id)
    {
        $device = new Device;
        $device->customerId = $id;
        $device->UUID = uniqid();
        $device->platformId = 2;
        $device->platformVersion = "1.0";
        $device->model = "smartphone $id";
        $device->notificationToken = uniqid();
        $device->location = new Point(48.875769, 2.342933);
        $device->save();
    }

    protected function insertAdditionalEntries($id)
    {
        $device = new Device;
        $device->customerId = $id;
        $device->UUID = uniqid();
        $device->platformId = 1;
        $device->platformVersion = "0.1";
        $device->model = 'NOKIA 3310';
        $device->notificationToken = uniqid();
        $device->location = new Point(48.875769, 2.342933);
        $device->save();
    }
}
