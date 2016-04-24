<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Customers\Entities\Address;
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

    public function handle(): Address
    {
        if ($this->customer->address) {
            $address = $this->customer->address;
            $address->fill($this->addressData);
        } else {
            $address = new Address($this->addressData);
            $address->customer()->associate($this->customer);
        }

        $address->save();

        return $address;
    }
}
