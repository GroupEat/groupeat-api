<?php
namespace Groupeat\Customers\Handlers\Commands;

use Groupeat\Customers\Commands\ChangeAddress;
use Groupeat\Customers\Entities\Address;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;

class ChangeAddressHandler
{
    private $addressConstraints;

    public function __construct(AddressConstraints $addressConstraints)
    {
        $this->addressConstraints = $addressConstraints->value();
    }

    /**
     * @param ChangeAddress $command
     *
     * @return Address
     */
    public function handle(ChangeAddress $command)
    {
        $attributes = $command->getAddressData();
        $customer = $command->getCustomer();

        if ($customer->address) {
            $address = $customer->address;
            $address->fill($attributes);
        } else {
            $address = new Address($attributes);
            $address->customer_id = $customer->id;
        }

        $address->fill($this->addressConstraints);

        $address->save();

        return $address;
    }
}
