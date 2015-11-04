<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Events\NotificationHasBeenSent;
use Groupeat\Notifications\Services\SendGcmNotification;
use Groupeat\Notifications\Values\NotificationsEnabled;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;
use RuntimeException;

class SendNotification
{
    private $events;
    private $enabled;
    private $gcm;
    private $apns;

    public function __construct(
        NotificationsEnabled $enabled,
        Dispatcher $events,
        SendGcmNotification $gcm,
        SendApnsNotification $apns
    ) {
        $this->enabled = $enabled->value();
        $this->events = $events;
        $this->gcm = $gcm;
        $this->apns = $apns;
    }

    public function call(Notification $notification, $force = false)
    {
        if ($this->enabled || $force) {
            $platformLabel = $notification->device->platform->label;

            switch ($platformLabel) {
                case 'android':
                    $this->gcm->call($notification);
                    break;

                case 'ios':
                    $this->apns->call($notification);
                    break;

                default:
                    throw new RuntimeException("Cannot send notification to platform $platformLabel");
            }
        }

        $notification->save();

        $this->events->fire(new NotificationHasBeenSent($notification));
    }
}
