<?php
namespace Groupeat\Devices\Handlers\Commands;

use Groupeat\Devices\Events\DeviceHasBeenAttached;
use Groupeat\Devices\Commands\AttachDevice;
use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;

class AttachDeviceHandler
{
    private $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function handle(AttachDevice $command)
    {
        $hardwareId = $command->getUUID();

        $this->assertNotAlreadyExisting($hardwareId);

        $device = new Device;
        $device->customer()->associate($command->getCustomer());
        $device->UUID = $command->getUUID();
        $device->notificationToken = $command->getNotificationToken();
        $device->platform()->associate($command->getPlatform());
        $device->version = $command->getVersion();
        $device->model = $command->getModel();
        $device->latitude = $command->getLatitude();
        $device->longitude = $command->getLongitude();

        $device->save();
        $this->events->fire(new DeviceHasBeenAttached($device));
    }

    private function assertNotAlreadyExisting($UUID)
    {
        if (Device::where('UUID', $UUID)->exists()) {
            throw new UnprocessableEntity(
                'deviceAlreadyExists',
                "The device #$UUID already exists."
            );
        }
    }
}
