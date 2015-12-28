<?php
namespace Groupeat\Notifications\Listeners;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\CheckDeviceCloseEnoughToJoinGroupOrder;
use Groupeat\Notifications\Services\SendJoinGroupOrderNotification;
use Groupeat\Notifications\Services\SelectDevicesToNotify;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;

class SendNotificationsToCustomersOnGoupOrderCreation extends QueuedListener
{
    private $selectDevicesToNotify;
    private $checkDeviceCloseEnoughToJoinGroupOrder;
    private $sendJoinGroupOrderNotification;

    public function __construct(
        SelectDevicesToNotify $selectDevicesToNotify,
        CheckDeviceCloseEnoughToJoinGroupOrder $checkDeviceCloseEnoughToJoinGroupOrder,
        SendJoinGroupOrderNotification $sendJoinGroupOrderNotification
    ) {
        $this->selectDevicesToNotify = $selectDevicesToNotify;
        $this->checkDeviceCloseEnoughToJoinGroupOrder = $checkDeviceCloseEnoughToJoinGroupOrder;
        $this->sendJoinGroupOrderNotification = $sendJoinGroupOrderNotification;
    }

    public function handle(GroupOrderHasBeenCreated $event)
    {
        $groupOrder = $event->getOrder()->groupOrder;

        $this->selectDevicesToNotify->call($groupOrder)->each(function (Device $device) use ($groupOrder) {
            $silent = !$this->checkDeviceCloseEnoughToJoinGroupOrder->call($device, $groupOrder);
            $this->sendJoinGroupOrderNotification->call($groupOrder, $device, $silent);
        });
    }
}
