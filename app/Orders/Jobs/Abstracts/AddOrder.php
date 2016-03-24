<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Support\ProductFormats;
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
}
