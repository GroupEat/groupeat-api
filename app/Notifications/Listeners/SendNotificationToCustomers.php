<?php
namespace Groupeat\Notifications\Listeners;

use Carbon\Carbon;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Devices\Entities\Device;
use Groupeat\Notifications\Entities\Notification as NotificationEntity;
use Groupeat\Notifications\Services\SendNotification;
use Groupeat\Notifications\Services\SelectDevicesToNotify;
use Groupeat\Notifications\Values\Notification as NotificationValue;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Support\Exceptions\Exception;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Groupeat\Support\Services\Locale;
use Psr\Log\LoggerInterface;

class SendNotificationToCustomers extends QueuedListener
{
    private $logger;
    private $locale;
    private $selectDevicesToNotify;
    private $sendNotification;

    public function __construct(
        LoggerInterface $logger,
        Locale $locale,
        SelectDevicesToNotify $selectDevicesToNotify,
        SendNotification $sendNotification
    ) {
        $this->logger = $logger;
        $this->locale = $locale;
        $this->selectDevicesToNotify = $selectDevicesToNotify;
        $this->sendNotification = $sendNotification;
    }

    public function handle(GroupOrderHasBeenCreated $event)
    {
        $groupOrder = $event->getOrder()->groupOrder;

        $this->selectDevicesToNotify->call($groupOrder)
            ->each(function (Device $device) use ($groupOrder) {
                $entity = new NotificationEntity;
                $entity->customer()->associate($device->customer);
                $entity->device()->associate($device);
                $entity->groupOrder()->associate($groupOrder);
                $value = $this->getValueFromEntity($entity);

                try {
                    $this->sendNotification->call($value);
                    $entity->save();
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

    public function getValueFromEntity(NotificationEntity $entity)
    {
        $customer = $entity->customer;
        $groupOrder = $entity->groupOrder;
        $maximumDiscountRate = $groupOrder->restaurant->maximumDiscountRate;
        $timeToLiveInSeconds = $groupOrder->endingAt->diffInSeconds(Carbon::now(), true);

        return new NotificationValue(
            $entity->device,
            $this->translateFor('title', $customer),
            $this->translateFor('message', $customer, compact('maximumDiscountRate')),
            $timeToLiveInSeconds,
            ['groupOrderId' => $groupOrder->id]
        );
    }

    protected function translateFor($messageKey, Customer $customer, array $params = [])
    {
        return $this->locale->executeWithUserLocale(function () use ($messageKey, $params) {
            return $this->locale->getTranslator()->get("notifications::messages.$messageKey", $params);
        }, $customer->credentials->locale);
    }
}
