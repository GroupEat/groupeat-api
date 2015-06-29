<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Jobs\Abstracts\Job;

abstract class AddOrder extends Job
{
    private $productFormats;
    private $deliveryAddressData;
    private $comment;

    public function __construct(
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
    ) {
        $this->productFormats = new ProductFormats($productFormats);
        $this->deliveryAddressData = $deliveryAddressData;
        $this->comment = $comment;
    }

    public function getProductFormats()
    {
        return $this->productFormats;
    }

    public function getDeliveryAddressData()
    {
        return $this->deliveryAddressData;
    }

    public function getComment()
    {
        return $this->comment;
    }
}
