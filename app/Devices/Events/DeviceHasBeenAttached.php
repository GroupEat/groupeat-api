<?php
namespace Groupeat\Devices\Events;

use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Events\Abstracts\Event;

class DeviceHasBeenAttached extends Event
{
    private $device;

    public function __construct(Device $device)
    {
        $this->device = $device;
    }

    public function getDevice()
    {
        return $this->device;
    }
}
