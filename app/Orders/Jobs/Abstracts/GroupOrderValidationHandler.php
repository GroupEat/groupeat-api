<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Customers\Values\AddressConstraints;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Contracts\Events\Dispatcher;
use Groupeat\Support\Values\Abstracts\DistanceInKms;

abstract class GroupOrderValidationHandler
{
    protected $events;
    protected $deliveryAddressConstraints;
    protected $maximumDistanceInKms;

    public function __construct(
        Dispatcher $events,
        AddressConstraints $addressConstraints,
        DistanceInKms $maximumDistanceInKms
    ) {
        $this->events = $events;
        $this->deliveryAddressConstraints = $addressConstraints->value();
        $this->maximumDistanceInKms = $maximumDistanceInKms->value();
    }

    protected function getDeliveryAddress(array $addressData)
    {
        return new DeliveryAddress(array_merge(
            $addressData,
            $this->deliveryAddressConstraints
        ));
    }

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
