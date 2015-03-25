<?php
namespace Groupeat\Customers\Commands;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Commands\Abstracts\Command;

class UpdateAddress extends Command
{
    private $customer;
    private $addressData;

    /**
     * @param Customer $customer
     * @param array    $addressData
     */
    public function __construct(Customer $customer, array $addressData)
    {
        $this->customer = $customer;
        $this->addressData = $addressData;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function getAddressData()
    {
        return $this->addressData;
    }
}
