<?php
namespace Groupeat\Devices\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\DeviceLocation;
use Groupeat\Devices\Services\MaintainCorrectDeviceOwner;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Events\NotificationHasBeenReceived;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Events\Dispatcher;
use Phaza\LaravelPostgis\Geometries\Point;

class UpdateDevice extends Job
{
    private $device;
    private $customer;
    private $platformVersion;
    private $notificationToken;
    private $notificationId;
    private $location;

    public function __construct(
        Device $device,
        Customer $customer,
        string $platformVersion,
        string $notificationToken,
        string $notificationId,
        Point $location = null
    ) {
        $this->device = $device;
        $this->customer = $customer;
        $this->platformVersion = $platformVersion;
        $this->notificationToken = $notificationToken;
        $this->notificationId = $notificationId;
        $this->location = $location;
    }

    public function handle(Dispatcher $events, MaintainCorrectDeviceOwner $maintainCorrectDeviceOwner)
    {
        $maintainCorrectDeviceOwner->call($this->device, $this->customer);

        if ($this->location) {
            DeviceLocation::createFromDeviceAndLocation($this->device, $this->location);
        }

        if ($this->platformVersion || $this->notificationToken) {
            if ($this->platformVersion) {
                $this->device->platformVersion = $this->platformVersion;
            }
            if ($this->notificationToken) {
                $this->device->notificationToken = $this->notificationToken;
            }
            $this->device->save();
        }

        if ($this->notificationId) {
            $events->fire(new NotificationHasBeenReceived(Notification::findOrFail($this->notificationId)));
        }

        return $this->device;
    }
}
