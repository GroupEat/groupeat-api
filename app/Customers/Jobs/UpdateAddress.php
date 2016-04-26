<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Customers\Entities\Address;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\DefaultAddressAttributes;
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

    public function handle(DefaultAddressAttributes $defaultAddressAttributes): Address
    {
        $addressAttributes = array_merge($defaultAddressAttributes->value(), $this->addressData);

        if ($this->customer->address) {
            $address = $this->customer->address;
            $address->fill($addressAttributes);
        } else {
            $address = new Address($addressAttributes);
            $address->customer()->associate($this->customer);
        }

        $address->save();

        return $address;
    }
}
