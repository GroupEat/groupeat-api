<?php
namespace Groupeat\Devices\Jobs;

use Groupeat\Devices\Entities\DeviceLocation;
use Groupeat\Devices\Events\DeviceHasBeenAttached;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Services\MaintainCorrectDeviceOwner;
use Illuminate\Contracts\Events\Dispatcher;

class AttachDeviceHandler
{
    private $events;
    private $maintainCorrectDeviceOwner;

    public function __construct(Dispatcher $events, MaintainCorrectDeviceOwner $maintainCorrectDeviceOwner)
    {
        $this->events = $events;
        $this->maintainCorrectDeviceOwner = $maintainCorrectDeviceOwner;
    }

    public function handle(AttachDevice $job)
    {
        $deviceUUID = $job->getUUID();
        $device = Device::where('UUID', $deviceUUID)->first();

        if (!is_null($device)) {
            $this->maintainCorrectDeviceOwner->call($device, $job->getCustomer());
        } else {
            $device = new Device;
            $device->customer()->associate($job->getCustomer());
            $device->UUID = $deviceUUID;
            $device->notificationToken = $job->getNotificationToken();
            $device->platform()->associate($job->getPlatform());
            $device->platformVersion = $job->getPlatformVersion();
            $device->model = $job->getModel();

            $device->save();
            $this->events->fire(new DeviceHasBeenAttached($device));
        }

        if ($job->getLocation()) {
            DeviceLocation::createFromDeviceAndLocationArray($device, $job->getLocation());
        }

        return $device;
    }
}
