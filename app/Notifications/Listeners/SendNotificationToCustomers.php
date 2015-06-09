<?php
namespace Groupeat\Notifications\Listeners;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\SendNotification;
use Groupeat\Notifications\Services\SelectDevicesToNotify;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Listeners\Abstracts\QueuedHandler;
use Psr\Log\LoggerInterface;

class SendNotificationToCustomers extends QueuedHandler
{
    private $logger;
    private $selectDevicesToNotify;
    private $sendNotification;

    public function __construct(
        LoggerInterface $logger,
        SelectDevicesToNotify $selectDevicesToNotify,
        SendNotification $sendNotification
    ) {
        $this->logger = $logger;
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

                try {
                    $this->sendNotification->call($notification);
                } catch (Exception $groupeatException) {
                    $this->logger->critical(
                        'Failed to send notification to '
                        . $device->customer->toShortString()
                        . ' on ' . $device->toShortString()
                        . ' for ' . $groupOrder->toShortString()
                        . ' with message ' . $groupeatException->getMessage()
                        . ' with trace ' . $groupeatException->getTraceAsString()
                    );
                }
            });
    }
}
