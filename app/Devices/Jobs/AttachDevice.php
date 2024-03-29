<?php
namespace Groupeat\Devices\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\DeviceLocation;
use Groupeat\Devices\Entities\Platform;
use Groupeat\Devices\Events\DeviceHasBeenAttached;
use Groupeat\Devices\Services\MaintainCorrectDeviceOwner;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Events\Dispatcher;
use Phaza\LaravelPostgis\Geometries\Point;

class AttachDevice extends Job
{
    private $customer;
    private $UUID;
    private $notificationToken;
    private $platform;
    private $platformVersion;
    private $model;
    private $location;

    public function __construct(
        Customer $customer,
        string $UUID,
        Platform $platform,
        string $platformVersion,
        string $model,
        string $notificationToken,
        Point $location = null
    ) {
        $this->customer = $customer;
        $this->UUID = $UUID;
        $this->platform = $platform;
        $this->platformVersion = $platformVersion;
        $this->model = $model;
        $this->location = $location;

        if ($notificationToken) {
            $this->notificationToken = $notificationToken;
        }
    }

    public function handle(Dispatcher $events, MaintainCorrectDeviceOwner $maintainCorrectDeviceOwner): Device
    {
        $device = Device::where('UUID', $this->UUID)->first();

        if ($device) {
            $maintainCorrectDeviceOwner->call($device, $this->customer);
        } else {
            $device = new Device;
            $device->customer()->associate($this->customer);
            $device->UUID = $this->UUID;
            $device->platform()->associate($this->platform);
            $device->model = $this->model;
        }

        $device->notificationToken = $this->notificationToken;
        $device->platformVersion = $this->platformVersion;

        $device->save();
        $events->fire(new DeviceHasBeenAttached($device));

        if ($this->location) {
            DeviceLocation::createFromDeviceAndLocation($device, $this->location);
        }

        return $device;
    }
}
