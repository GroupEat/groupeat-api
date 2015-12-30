<?php
namespace Groupeat\Notifications\Values;

use Groupeat\Devices\Entities\Device;

class SilentNotification extends Notification
{
    public function __construct(Device $device, int $timeToLiveInSeconds, array $additionalData = [])
    {
        parent::__construct($device, $timeToLiveInSeconds, $additionalData, '', '');
    }
}
