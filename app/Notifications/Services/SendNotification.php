<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\SendGcmNotification;
use Groupeat\Support\Exceptions\Exception;

class SendNotification
{
    private $gcm;

    public function __construct(SendGcmNotification $gcm)
    {
        $this->gcm = $gcm;
    }

    public function call(Notification $notification)
    {
        $platformLabel = $notification->device->platform->label;

        switch ($platformLabel) {
            case 'android':
                return $this->gcm->call($notification);
                break;

            default:
                throw new Exception("Cannot send notification to platfrom $platformLabel");
        }
    }
}
