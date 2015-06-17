<?php
namespace Groupeat\Devices\Jobs;

use Groupeat\Devices\Events\DeviceHasBeenAttached;
use Groupeat\Devices\Jobs\AttachDevice;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Services\ChangeDeviceOwner;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;

class AttachDeviceHandler
{
    private $events;
    private $changeDeviceOwner;

    public function __construct(Dispatcher $events, ChangeDeviceOwner $changeDeviceOwner)
    {
        $this->events = $events;
        $this->changeDeviceOwner = $changeDeviceOwner;
    }

    public function handle(AttachDevice $job)
    {
        $deviceUUID = $job->getUUID();
        $device = Device::where('UUID', $deviceUUID)->first();

        if (!is_null($device)) {
            $this->changeDeviceOwner->call($device, $job->getCustomer());
        } else {
            $device = new Device;
            $device->customer()->associate($job->getCustomer());
            $device->UUID = $deviceUUID;
            $device->notificationToken = $job->getNotificationToken();
            $device->platform()->associate($job->getPlatform());
            $device->model = $job->getModel();

            $device->save();
            $this->events->fire(new DeviceHasBeenAttached($device));
        }

        return $device;
    }
}
