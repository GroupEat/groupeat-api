<?php
namespace Groupeat\Notifications\Handlers\Events;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\SendNotification;
use Groupeat\Notifications\Services\SelectDevicesToNotify;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Handlers\Events\Abstracts\QueuedHandler;

class SendNotificationToCustomers extends QueuedHandler
{
    private $selectDevicesToNotify;
    private $sendNotification;

    public function __construct(
        SelectDevicesToNotify $selectDevicesToNotify,
        SendNotification $sendNotification
    ) {
        $this->selectDevicesToNotify = $selectDevicesToNotify;
        $this->sendNotification = $sendNotification;
    }

    public function handle(GroupOrderHasBeenCreated $groupOrderHasBeenCreated)
    {
        $groupOrder = $groupOrderHasBeenCreated->getOrder()->groupOrder;

        $this->selectDevicesToNotify->call($groupOrder)
            ->each(function (Device $device) use ($groupOrder) {
                $notification = new Notification;
                $notification->customer()->associate($device->customer);
                $notification->device()->associate($device);
                $notification->groupOrder()->associate($groupOrder);
                $notification->longitude = $device->longitude;
                $notification->latitude = $device->latitude;

                $this->sendNotification->call($notification);
            });
    }
}
