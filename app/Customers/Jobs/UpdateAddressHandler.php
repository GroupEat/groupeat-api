<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Customers\Jobs\UpdateAddress;
use Groupeat\Customers\Entities\Address;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;

class UpdateAddressHandler
{
    private $addressConstraints;

    public function __construct(AddressConstraints $addressConstraints)
    {
        $this->addressConstraints = $addressConstraints->value();
    }

    /**
     * @param UpdateAddress $command
     *
     * @return Address
     */
    public function handle(UpdateAddress $command)
    {
        $attributes = $command->getAddressData();
        $customer = $command->getCustomer();

        if ($customer->address) {
            $address = $customer->address;
            $address->fill($attributes);
        } else {
            $address = new Address($attributes);
            $address->customerId = $customer->id;
        }

        $address->fill($this->addressConstraints);

        $address->save();

        return $address;
    }
}
