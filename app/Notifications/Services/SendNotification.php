<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Events\NotificationHasBeenSent;
use Groupeat\Notifications\Values\Notification;
use Groupeat\Notifications\Values\NotificationsEnabled;
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

    public function call(Notification $notification, bool $force = false): string
    {
        if ($this->enabled || $force) {
            $platformLabel = $notification->getDevice()->platform->label;

            switch ($platformLabel) {
                case 'android':
                    $response = $this->gcm->call($notification);
                    break;

                case 'ios':
                    $response = (string) $this->apns->call($notification);
                    break;

                default:
                    throw new RuntimeException("Cannot send notification to platform $platformLabel");
            }
        } else {
            $response = 'notSent';
        }

        $this->events->fire(new NotificationHasBeenSent($notification));

        return $response;
    }
}
