<?php
namespace Groupeat\Notifications\Values;

use Groupeat\Devices\Entities\Device;

class SilentNotification extends Notification
{
    public function __construct(Device $device, $timeToLiveInSeconds, $additionalData = [])
    {
        parent::__construct($device, null, null, $timeToLiveInSeconds, $additionalData);
    }
}
