<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Customers\Entities\Address;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
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

    public function handle(AddressConstraints $addressConstraints): Address
    {
        if ($this->customer->address) {
            $address = $this->customer->address;
            $address->fill($this->addressData);
        } else {
            $address = new Address($this->addressData);
            $address->customerId = $this->customer->id;
        }

        $address->fill($addressConstraints->value());
        $address->save();

        return $address;
    }
}
