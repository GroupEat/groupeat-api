<?php namespace Groupeat\Orders\Services\Abstracts;

use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Illuminate\Events\Dispatcher;

abstract class GroupOrderValidation {

    /**
     * @var Dispatcher
     */
    protected $events;

    /**
     * @var int
     */
    protected $maximumDeliveryDistanceInKms;

    /**
     * @var array
     */
    protected $deliveryAddressConstraints;


    public function __construct(
        Dispatcher $events,
        $maximumDeliveryDistanceInKms,
        array $deliveryAddressConstraints
    )
    {
        $this->events = $events;
        $this->maximumDeliveryDistanceInKms = (float) $maximumDeliveryDistanceInKms;
        $this->deliveryAddressConstraints = $deliveryAddressConstraints;
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

        if ($distanceInKms > $this->maximumDeliveryDistanceInKms)
        {
            throw new UnprocessableEntity(
                'deliveryDistanceTooLong',
                'The delivery distance should be less than '
                . $this->maximumDeliveryDistanceInKms . " kms, $distanceInKms given."
            );
        }
    }

}
