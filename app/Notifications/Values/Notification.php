<?php
namespace Groupeat\Notifications\Values;

use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Exceptions\BadRequest;

class Notification
{
    private $device;
    private $timeToLiveInSeconds;
    private $additionalData;
    private $title;
    private $message;

    // The $additionalData associative array will be merged with the notification payload.
    // It should not contain any key used by GCM or APNS.
    public function __construct(
        Device $device,
        int $timeToLiveInSeconds,
        array $additionalData = [],
        string $title = '',
        string $message = ''
    ) {
        if (empty($device->notificationToken)) {
            throw new BadRequest(
                'missingNotificationToken',
                "Cannot send notification to {$device->toShortString()} without token"
            );
        }

        $this->device = $device;
        $this->title = $title;
        $this->message = $message;
        $this->timeToLiveInSeconds = $timeToLiveInSeconds;
        $this->additionalData = $additionalData;
    }

    public function getDevice()
    {
        return $this->device;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getTimeToLiveInSeconds()
    {
        return $this->timeToLiveInSeconds;
    }

    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    public function isSilent()
    {
        return empty($this->message) && empty($this->title);
    }
}
