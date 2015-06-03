<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Events\NotificationWouldHaveBeenSent;
use Groupeat\Notifications\Services\SendGcmNotification;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Values\Environment;
use Illuminate\Events\Dispatcher;
use RuntimeException;

class SendNotification
{
    private $environment;
    private $events;
    private $gcm;
    private $apns;

    public function __construct(
        Environment $environment,
        Dispatcher $events,
        SendGcmNotification $gcm,
        SendApnsNotification $apns
    ) {
        $this->environment = $environment;
        $this->events = $events;
        $this->gcm = $gcm;
        $this->apns = $apns;
    }

    public function call(Notification $notification, $force = false)
    {
        if ($force || $this->needToSend()) {
            $platformLabel = $notification->device->platform->label;

            switch ($platformLabel) {
                case 'android':
                    $this->gcm->call($notification);
                    break;

                case 'ios':
                    $this->apns->call($notification);
                    break;

                default:
                    throw new RuntimeException("Cannot send notification to platfrom $platformLabel");
            }
        } else {
            $this->events->fire(new NotificationWouldHaveBeenSent($notification));
        }

        $notification->save();
    }

    private function needToSend()
    {
        return $this->environment->is('production') || $this->environment->is('staging');
    }
}
