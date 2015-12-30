<?php
namespace Groupeat\Devices\Jobs;

use Groupeat\Devices\Entities\DeviceLocation;
use Groupeat\Devices\Services\MaintainCorrectDeviceOwner;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Events\NotificationHasBeenReceived;
use Illuminate\Contracts\Events\Dispatcher;

class UpdateDeviceHandler
{
    private $events;
    private $maintainCorrectDeviceOwner;

    public function __construct(Dispatcher $events, MaintainCorrectDeviceOwner $maintainCorrectDeviceOwner)
    {
        $this->events = $events;
        $this->maintainCorrectDeviceOwner = $maintainCorrectDeviceOwner;
    }

    public function handle(UpdateDevice $job)
    {
        $device = $job->getDevice();
        $platformVersion = $job->getPlatformVersion();
        $notificationToken = $job->getNotificationToken();
        $notificationId = $job->getNotificationId();

        $this->maintainCorrectDeviceOwner->call($device, $job->getCustomer());

        $location = $job->getLocation();

        if (!empty($location)) {
            DeviceLocation::createFromDeviceAndLocationArray($device, $location);
        }

        if ($platformVersion || $notificationToken) {
            if ($platformVersion) {
                $device->platformVersion = $platformVersion;
            }
            if ($notificationToken) {
                $device->notificationToken = $notificationToken;
            }
            $device->save();
        }

        if ($notificationId) {
            $this->events->fire(new NotificationHasBeenReceived(Notification::findOrFail($notificationId)));
        }

        return $device;
    }
}
