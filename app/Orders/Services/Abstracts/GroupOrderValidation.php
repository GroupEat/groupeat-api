<?php
namespace Groupeat\Orders\Services\Abstracts;

use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasEnded;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Events\Abstracts\Event;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Events\Dispatcher;

abstract class GroupOrderValidation
{
    protected $events;
    protected $maximumDeliveryDistanceInKms;
    protected $deliveryAddressConstraints;

    public function __construct(
        Dispatcher $events,
        MaximumDeliveryDistanceInKms $maximumDeliveryDistanceInKms,
        AddressConstraints $addressConstraints
    ) {
        $this->events = $events;
        $this->maximumDeliveryDistanceInKms = $maximumDeliveryDistanceInKms->value();
        $this->deliveryAddressConstraints = $addressConstraints->value();
    }

    /**
     * @param array $addressData
     *
     * @return DeliveryAddress
     */
    protected function getDeliveryAddress(array $addressData)
    {
        return new DeliveryAddress(array_merge(
            $addressData,
            $this->deliveryAddressConstraints
        ));
    }

    /**
     * @param DeliveryAddress $deliveryAddress
     * @param Address         $other
     */
    protected function assertCloseEnough(DeliveryAddress $deliveryAddress, Address $other)
    {
        $distanceInKms = $deliveryAddress->distanceInKmsWith($other);

        if ($distanceInKms > $this->maximumDeliveryDistanceInKms) {
            throw new UnprocessableEntity(
                'deliveryDistanceTooLong',
                'The delivery distance should be less than '
                .$this->maximumDeliveryDistanceInKms." kms, $distanceInKms given."
            );
        }
    }

    protected function fireSuitableEvents(Order $order, Event $event)
    {
        $groupOrder = $order->groupOrder;

        $this->events->fire($event);

        if (!$groupOrder->isJoinable()) {
            $this->events->fire(new GroupOrderHasEnded($groupOrder));
        }
    }
}
