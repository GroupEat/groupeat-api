<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\SendGcmNotification;
use Groupeat\Support\Exceptions\Exception;

class SendNotification
{
    private $gcm;
    private $apns;

    public function __construct(SendGcmNotification $gcm, SendApnsNotification $apns)
    {
        $this->gcm = $gcm;
        $this->apns = $apns;
    }

    public function call(Notification $notification)
    {
        $platformLabel = $notification->device->platform->label;

        switch ($platformLabel) {
            case 'android':
                $this->gcm->call($notification);
                break;

            case 'ios':
                $this->apns->call($notification);
                break;

            default:
                throw new Exception("Cannot send notification to platfrom $platformLabel");
        }
    }
}
