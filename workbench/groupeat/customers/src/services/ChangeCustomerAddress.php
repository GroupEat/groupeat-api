<?php
namespace Groupeat\Customers\Services;

use Groupeat\Customers\Entities\Address;
use Groupeat\Customers\Entities\Customer;

class ChangeCustomerAddress
{
    /**
     * @var array
     */
    private $addressConstraints;

    public function __construct(array $addressConstraints)
    {
        $this->addressConstraints = $addressConstraints;
    }

    /**
     * @param Customer $customer
     * @param array    $attributes
     *
     * @return Address
     */
    public function call(Customer $customer, $attributes)
    {
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
