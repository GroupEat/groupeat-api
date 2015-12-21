<?php
namespace Groupeat\Notifications\Events;

use Groupeat\Notifications\Values\Notification;
use Groupeat\Support\Events\Abstracts\Event;

class NotificationHasBeenSent extends Event
{
    private $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function getNotification()
    {
        return $this->notification;
    }
}
