<?php
namespace Groupeat\Orders\Jobs\Abstracts;

use Groupeat\Customers\Entities\Customer;

abstract class AddCustomerOrder extends AddOrder
{
    private $customer;

    public function __construct(
        Customer $customer,
        array $productFormats,
        array $deliveryAddressData,
        $comment = null
    ) {
        parent::__construct($productFormats, $deliveryAddressData, $comment);

        $this->customer = $customer;
    }

    public function getCustomer()
    {
        return $this->customer;
    }
}
