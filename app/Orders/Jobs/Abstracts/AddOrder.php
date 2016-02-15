<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Entities\Abstracts\Address;
use Groupeat\Support\Exceptions\UnprocessableEntity;
use Groupeat\Support\Jobs\Abstracts\Job;

abstract class AddOrder extends Job
{
    protected $productFormats;
    protected $deliveryAddressData;
    protected $comment;

    public function __construct(
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
    ) {
        $this->productFormats = new ProductFormats($productFormats);
        $this->deliveryAddressData = $deliveryAddressData;
        $this->comment = $comment;
    }

    protected function getDeliveryAddress(array $addressData, array $deliveryAddressConstraints)
    {
        return new DeliveryAddress(array_merge(
            $addressData,
            $deliveryAddressConstraints
        ));
    }

    protected function assertCloseEnough(DeliveryAddress $deliveryAddress, Address $other, float $maximumDistanceInKms)
    {
        $distanceInKms = $deliveryAddress->distanceInKmsWith($other);

        if ($distanceInKms > $maximumDistanceInKms) {
            throw new UnprocessableEntity(
                'deliveryDistanceTooLong',
                'The delivery distance should be less than '
                .$maximumDistanceInKms." kms, $distanceInKms given."
            );
        }
    }
}
