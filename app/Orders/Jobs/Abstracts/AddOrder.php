<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Devices\Entities\Device;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Jobs\Abstracts\Job;

abstract class AddOrder extends Job
{
    protected $productFormats;
    protected $deliveryAddress;
    protected $comment;

    public function __construct(array $productFormats, array $deliveryAddressData, $comment = null)
    {
        $this->productFormats = new ProductFormats($productFormats);
        $this->deliveryAddress = new DeliveryAddress($deliveryAddressData);
        $this->comment = $comment;
    }
}
