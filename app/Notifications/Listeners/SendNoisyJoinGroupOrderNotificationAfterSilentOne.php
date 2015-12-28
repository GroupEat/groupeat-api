<?php
namespace Groupeat\Notifications\Listeners;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Events\NotificationHasBeenReceived;
use Groupeat\Notifications\Services\CheckDeviceCloseEnoughToJoinGroupOrder;
use Groupeat\Notifications\Services\SendJoinGroupOrderNotification;
use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Psr\Log\LoggerInterface;

class SendNoisyJoinGroupOrderNotificationAfterSilentOne extends QueuedListener
{
    private $logger;
    private $checkDeviceCloseEnoughToJoinGroupOrder;
    private $sendJoinGroupOrderNotification;

    public function __construct(
        LoggerInterface $logger,
        CheckDeviceCloseEnoughToJoinGroupOrder $checkDeviceCloseEnoughToJoinGroupOrder,
        SendJoinGroupOrderNotification $sendJoinGroupOrderNotification
    ) {
        $this->logger = $logger;
        $this->checkDeviceCloseEnoughToJoinGroupOrder = $checkDeviceCloseEnoughToJoinGroupOrder;
        $this->sendJoinGroupOrderNotification = $sendJoinGroupOrderNotification;
    }

    public function handle(NotificationHasBeenReceived $event)
    {
        $notification = $event->getNotification();

        if ($this->needToSendNoisyNotification($event)) {
            $this->sendJoinGroupOrderNotification->call($notification->groupOrder, $notification->device);
        }
    }

    private function needToSendNoisyNotification(Notification $notification)
    {
        if (!$notification->silent) {
            $this->logWhyNotSending($notification, 'Not silent');
            return false;
        }

        if (!$notification->groupOrder) {
            $this->logWhyNotSending($notification, 'Not for group order');
            return false;
        }

        if (!$notification->groupOrder->isJoinable()) {
            $this->logWhyNotSending($notification, 'Not joinable');
            return false;
        }

        $device = $notification->device;
        $groupOrder = $notification->groupOrder;

        $customersIdsInGroupOrder = $groupOrder->orders->map(function (Order $order) {
            return $order->customer->id;
        })->all();

        if (in_array($device->customerId, $customersIdsInGroupOrder)) {
            $this->logWhyNotSending($notification, 'Customer already in group order');
            return false;
        }

        if (!$this->checkDeviceCloseEnoughToJoinGroupOrder->call($device, $groupOrder)) {
            $this->logWhyNotSending($notification, 'Device too far');
            return false;
        }

        return true;
    }

    private function logWhyNotSending(Notification $notification, $message)
    {
        $this->logger->debug("Not sending noisy join group order notification after silent one [$message]", [
           'notificationId' => $notification->id
        ]);
    }
}
