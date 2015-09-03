<?php
namespace Groupeat\Devices\Events;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Events\Abstracts\Event;

class DeviceOwnerHasChanged extends Event
{
    private $device;
    private $oldOwner;
    private $newOwner;

    public function __construct(Device $device, Customer $oldOwner, Customer $newOwner)
    {
        $this->device = $device;
        $this->oldOwner = $oldOwner;
        $this->newOwner = $newOwner;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function getOldOwner()
    {
        return $this->oldOwner;
    }

    public function getNewOwner()
    {
        return $this->newOwner;
    }
}
