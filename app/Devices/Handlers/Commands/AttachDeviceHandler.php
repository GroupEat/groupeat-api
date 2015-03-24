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
        $hardwareId = $command->getHardwareId();

        $this->assertNotAlreadyExisting($hardwareId);

        $device = new Device;
        $device->customer()->associate($command->getCustomer());
        $device->hardwareId = $command->getHardwareId();
        $device->notificationToken = $command->getNotificationToken();
        $device->operatingSystem()->associate($command->getOperatingSystem());
        $device->operatingSystemVersion = $command->getOperatingSystemVersion();
        $device->model = $command->getModel();
        $device->latitude = $command->getLatitude();
        $device->longitude = $command->getLongitude();

        $device->save();
        $this->events->fire(new DeviceHasBeenAttached($device));
    }

    private function assertNotAlreadyExisting($hardwareId)
    {
        if (Device::where('hardwareId', $hardwareId)->exists()) {
            throw new UnprocessableEntity(
                'deviceAlreadyExists',
                "The device #$hardwareId already exists."
            );
        }
    }
}