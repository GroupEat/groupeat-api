<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Jobs\Abstracts\Job;

abstract class AddCustomerOrder extends Job
{
    private $customer;
    private $productFormats;
    private $deliveryAddressData;
    private $comment;

    public function __construct(
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
    ) {
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
        return $this->getComment();
    }
}
