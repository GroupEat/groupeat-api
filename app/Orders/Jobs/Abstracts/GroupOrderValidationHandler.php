<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Values\Abstracts\Value;
use Illuminate\Contracts\Events\Dispatcher;

abstract class GroupOrderValidationHandler
{
    protected $events;
    protected $deliveryAddressConstraints;
    protected $maximumDistanceInKms;

    public function __construct(
        Dispatcher $events,
        AddressConstraints $addressConstraints,
        Value $maximumDistanceInKms
    ) {
        $this->events = $events;
        $this->deliveryAddressConstraints = $addressConstraints->value();
        $this->maximumDistanceInKms = $maximumDistanceInKms->value();
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

        if ($distanceInKms > $this->maximumDistanceInKms) {
            throw new UnprocessableEntity(
                'deliveryDistanceTooLong',
                'The delivery distance should be less than '
                .$this->maximumDistanceInKms." kms, $distanceInKms given."
            );
        }
    }
}
