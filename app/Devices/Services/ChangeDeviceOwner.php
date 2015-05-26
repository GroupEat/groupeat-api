<?php
namespace Groupeat\Devices\Services;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Events\DeviceOwnerHasChanged;
use Illuminate\Contracts\Events\Dispatcher;

class ChangeDeviceOwner
{
    private $events;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function call(Device $device, Customer $newOwner)
    {
        if ($device->customer->id != $newOwner->id) {
            $oldOwner = $device->customer;
            $device->customer()->associate($newOwner);
            $device->save();

            $this->events->fire(new DeviceOwnerHasChanged($device, $oldOwner, $newOwner));
        }
    }
}
