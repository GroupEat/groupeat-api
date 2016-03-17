<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\DeviceLocation;
use Groupeat\Notifications\Values\DeviceLocationFreshnessInMinutes;
use Groupeat\Orders\Entities\GroupOrder;
use Psr\Log\LoggerInterface;

class CheckDeviceCloseEnoughToJoinGroupOrder
{
    private $logger;
    private $deviceLocationFreshnessInMinutes;

    public function __construct(
        LoggerInterface $logger,
        DeviceLocationFreshnessInMinutes $deviceLocationFreshnessInMinutes
    ) {
        $this->logger = $logger;
        $this->deviceLocationFreshnessInMinutes = $deviceLocationFreshnessInMinutes->value();
    }

    public function call(Device $device, GroupOrder $groupOrder): bool
    {
        $lastDeviceLocation = $device->locations()->latest(DeviceLocation::CREATED_AT)->first();

        if (!$lastDeviceLocation) {
            $this->logWhyNotCloseEnough($device, $groupOrder, "No available location for this device");
            return false;
        }

        if ($lastDeviceLocation->createdAt->diffInMinutes() > $this->deviceLocationFreshnessInMinutes) {
            $this->logWhyNotCloseEnough(
                $device,
                $groupOrder,
                "{$lastDeviceLocation->toShortString()} is older than $this->deviceLocationFreshnessInMinutes minutes"
            );
            return false;
        }

        if (!$groupOrder->isCloseEnoughToJoin($lastDeviceLocation->location)) {
            $this->logWhyNotCloseEnough(
                $device,
                $groupOrder,
                "The device is too far from the group order"
            );
            return false;
        }

        return true;
    }

    private function logWhyNotCloseEnough(Device $device, GroupOrder $groupOrder, $message)
    {
        $this->logger->debug("Device not close enough [$message]", [
            'device' => $device,
            'groupOrder' => $groupOrder
        ]);
    }
}
