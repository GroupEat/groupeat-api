<?php namespace Groupeat\Orders\Services\Abstracts;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Exceptions\UnprocessableEntity;

abstract class GroupOrderValidation {

    /**
     * @var int
     */
    protected $maximumDeliveryDistanceInKms;

    /**
     * @var array
     */
    protected $deliveryAddressConstraints;


    public function __construct($maximumDeliveryDistanceInKms, array $deliveryAddressConstraints)
    {
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
     * @param Customer $customer
     */
    protected function assertActivatedCustomer(Customer $customer)
    {
        $customer->assertActivated("The {$customer->toShortString()} should be activated to place an order.");
    }

    /**
     * @param DeliveryAddress $deliveryAddress
     * @param Restaurant      $restaurant
     */
    protected function assertCloseEnough(DeliveryAddress $deliveryAddress, Restaurant $restaurant)
    {
        $distanceInKms = $deliveryAddress->distanceInKmsWith($restaurant->address);

        if ($distanceInKms > $this->maximumDeliveryDistanceInKms)
        {
            throw new UnprocessableEntity(
                'deliveryDistanceTooLong',
                'The distance between the given delivery address and the '
                . $restaurant->toShortString() . ' should be less than '
                . $this->maximumDeliveryDistanceInKms . ' kms.'
            );
        }
    }

}
