<?php
namespace Groupeat\Orders\Commands\Abstracts;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Commands\Abstracts\Command;

abstract class PlaceOrder extends Command
{
    protected $customer;
    protected $productFormats;
    protected $deliveryAddressData;
    protected $comment;

    public function __construct(
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
    ) {
        $this->customer = $customer;
        $this->productFormats = new ProductFormats($productFormats);
        $this->deliveryAddressData = $deliveryAddressData;
        $this->comment = $comment;
    }

    public function getCustomer()
    {
        return $this->customer;
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
