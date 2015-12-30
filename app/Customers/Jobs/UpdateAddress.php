<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Jobs\Abstracts\Job;

class UpdateAddress extends Job
{
    private $customer;
    private $addressData;

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
