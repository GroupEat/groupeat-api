<?php
namespace Groupeat\Devices\Handlers\Commands;

use Groupeat\Devices\Events\DeviceHasBeenAttached;
use Groupeat\Devices\Commands\AttachDevice;
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

    public function handle(AttachDevice $command)
    {
        $deviceUUID = $command->getUUID();
        $device = Device::where('UUID', $deviceUUID)->first();

        if (!is_null($device)) {
            $this->changeDeviceOwner->call($device, $command->getCustomer());
        } else {
            $device = new Device;
            $device->customer()->associate($command->getCustomer());
            $device->UUID = $deviceUUID;
            $device->notificationToken = $command->getNotificationToken();
            $device->platform()->associate($command->getPlatform());
            $device->model = $command->getModel();

            $device->save();
            $this->events->fire(new DeviceHasBeenAttached($device));
        }

        return $device;
    }
}
