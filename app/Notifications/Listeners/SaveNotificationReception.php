<?php
namespace Groupeat\Notifications\Listeners;

use Groupeat\Notifications\Events\NotificationHasBeenReceived;

class SaveNotificationReception
{
    public function handle(NotificationHasBeenReceived $event)
    {
        $notification = $event->getNotification();
        $notification->receivedAt = $notification->freshTimestamp();
        $notification->save();
    }
}
