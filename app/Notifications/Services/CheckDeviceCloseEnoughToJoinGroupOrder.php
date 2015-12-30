<?php
namespace Groupeat\Notifications\Services;

use Groupeat\Devices\Entities\Device;
use Groupeat\Devices\Entities\DeviceLocation;
use Groupeat\Notifications\Values\DeviceLocationFreshnessInMinutes;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Values\JoinableDistanceInKms;
use Psr\Log\LoggerInterface;

class CheckDeviceCloseEnoughToJoinGroupOrder
{
    private $logger;
    private $deviceLocationFreshnessInMinutes;
    private $joinableDistanceInKms;

    public function __construct(
        LoggerInterface $logger,
        DeviceLocationFreshnessInMinutes $deviceLocationFreshnessInMinutes,
        JoinableDistanceInKms $joinableDistanceInKms
    ) {
        $this->logger = $logger;
        $this->deviceLocationFreshnessInMinutes = $deviceLocationFreshnessInMinutes->value();
        $this->joinableDistanceInKms = $joinableDistanceInKms->value();
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

        $groupOrderAddress = $groupOrder->getAddressToCompareToForJoining();
        $distanceWithGroupOrder = $groupOrderAddress->distanceInKmsWithPoint($lastDeviceLocation->location);

        if ($distanceWithGroupOrder > $this->joinableDistanceInKms) {
            $this->logWhyNotCloseEnough(
                $device,
                $groupOrder,
                "$distanceWithGroupOrder kms is more than $this->joinableDistanceInKms kms"
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
