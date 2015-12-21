<?php
namespace Groupeat\Notifications\Values;

use Groupeat\Devices\Entities\Device;
use Groupeat\Support\Exceptions\BadRequest;

class Notification
{
    /**
     * @var Device
     */
    private $device;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $timeToLiveInSeconds;

    /**
     * @var array Associative array that will be merged with the notification payload.
     *            It should not contain any key used by GCM or APNS.
     */
    private $additionalData;

    public function __construct(Device $device, $title, $message, $timeToLiveInSeconds, $additionalData = [])
    {
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
