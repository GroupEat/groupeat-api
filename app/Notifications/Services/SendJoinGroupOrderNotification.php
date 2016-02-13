<?php
namespace Groupeat\Notifications\Services;

use Exception;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Services\Locale;
use Psr\Log\LoggerInterface;

class SendJoinGroupOrderNotification
{
    private $logger;
    private $locale;
    private $sendNotification;

    public function __construct(LoggerInterface $logger, Locale $locale, SendNotification $sendNotification)
    {
        $this->logger = $logger;
        $this->locale = $locale;
        $this->sendNotification = $sendNotification;
    }

    public function call(
        GroupOrder $groupOrder,
        Device $device,
        bool $silent = false,
        array $additionalData = []
    ): Notification {
        $customer = $device->customer;
        $maximumDiscountRate = $groupOrder->restaurant->maximumDiscountRate;

        $entity = new Notification;
        $entity->silent = $silent;
        $entity->customer()->associate($device->customer);
        $entity->device()->associate($device);
        $entity->groupOrder()->associate($groupOrder);
        $entity->save();

        $additionalData['groupOrderId'] = $groupOrder->id;

        $value = $entity->toValue(
            $groupOrder->endingAt->diffInSeconds(),
            $additionalData,
            $this->translateFor('title', $customer),
            $this->translateFor('message', $customer, compact('maximumDiscountRate'))
        );

        try {
            $this->sendNotification->call($value);
        } catch (Exception $exception) {
            $entity->failed = true;
            $entity->save();
            $this->logger->critical(
                'Failed to send notification to '
                . $device->customer->toShortString()
                . ' on ' . $device->toShortString()
                . ' for ' . $groupOrder->toShortString()
                . ' with message ' . $exception->getMessage()
                . ' with trace ' . $exception->getTraceAsString()
            );
        }

        return $entity;
    }

    private function translateFor(string $messageKey, Customer $customer, array $params = []): string
    {
        return $this->locale->executeWithUserLocale(function () use ($messageKey, $params) {
            return $this->locale->getTranslator()->get("notifications::messages.$messageKey", $params);
        }, $customer->credentials->locale);
    }
}
