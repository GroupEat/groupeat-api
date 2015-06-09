<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Restaurants\Values\MaximumDeliveryDistanceInKms;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;

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
}
