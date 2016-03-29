<?php
namespace Groupeat\Notifications\Listeners;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Notifications\Services\CheckDeviceCloseEnoughToJoinGroupOrder;
use Groupeat\Notifications\Services\SendJoinGroupOrderNotification;
use Groupeat\Notifications\Services\SelectDevicesToNotify;
use Groupeat\Notifications\Values\MaximumNumberOfRiskyNotifications;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Psr\Log\LoggerInterface;

class SendNotificationsToCustomersOnGoupOrderCreation extends QueuedListener
{
    private $logger;
    private $selectDevicesToNotify;
    private $checkDeviceCloseEnoughToJoinGroupOrder;
    private $sendJoinGroupOrderNotification;
    private $maximumNumberOfRiskyNotifications;

    public function __construct(
        LoggerInterface $logger,
        SelectDevicesToNotify $selectDevicesToNotify,
        CheckDeviceCloseEnoughToJoinGroupOrder $checkDeviceCloseEnoughToJoinGroupOrder,
        SendJoinGroupOrderNotification $sendJoinGroupOrderNotification,
        MaximumNumberOfRiskyNotifications $maximumNumberOfRiskyNotifications
    ) {
        $this->logger = $logger;
        $this->selectDevicesToNotify = $selectDevicesToNotify;
        $this->checkDeviceCloseEnoughToJoinGroupOrder = $checkDeviceCloseEnoughToJoinGroupOrder;
        $this->sendJoinGroupOrderNotification = $sendJoinGroupOrderNotification;
        $this->maximumNumberOfRiskyNotifications = $maximumNumberOfRiskyNotifications->value();
    }

    public function handle(GroupOrderHasBeenCreated $event)
    {
        $groupOrder = $event->getOrder()->groupOrder;
        $devicesToNotify = $this->selectDevicesToNotify->call($groupOrder);

        $devicesToNotifyNoisilyWithoutRisk = $devicesToNotify->filter(function (Device $device) use ($groupOrder) {
            return $this->checkDeviceCloseEnoughToJoinGroupOrder->call($device, $groupOrder);
        });
        $remainingDevicesToNotify = $devicesToNotify->diff($devicesToNotifyNoisilyWithoutRisk);

        $riskyNoisyNotificationsCount = min(
            $remainingDevicesToNotify->count(),
            max(
                $this->maximumNumberOfRiskyNotifications - $devicesToNotifyNoisilyWithoutRisk->count(),
                0
            )
        );
        $devicesToNotifyRiskily = $riskyNoisyNotificationsCount > 0 ?
            $remainingDevicesToNotify->random($riskyNoisyNotificationsCount) : collect();
        $this->logger->info(
            "Sending risky noisy notifications to {$devicesToNotifyRiskily->pluck('id')}",
            compact($groupOrder)
        );

        $devicesToNotifySilently = $remainingDevicesToNotify->diff($devicesToNotifyRiskily);
        $devicesToNotifySilently->each(function (Device $device) use ($groupOrder) {
            $this->sendJoinGroupOrderNotification->call($groupOrder, $device, true);
        });

        $devicesToNotifyNoisily = $devicesToNotifyRiskily->merge($devicesToNotifyNoisilyWithoutRisk);
        $devicesToNotifyNoisily->each(function (Device $device) use ($groupOrder) {
            $this->sendJoinGroupOrderNotification->call($groupOrder, $device, false);
        });
    }
}
