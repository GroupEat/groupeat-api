<?php
namespace Groupeat\Customers\Jobs;

use Groupeat\Customers\Jobs\UpdateAddress;
use Groupeat\Customers\Entities\Address;
use Groupeat\Customers\Entities\Customer;
use Groupeat\Customers\Values\AddressConstraints;
use Phaza\LaravelPostgis\Geometries\Point;

class UpdateAddressHandler
{
    private $addressConstraints;

    public function __construct(AddressConstraints $addressConstraints)
    {
        $this->addressConstraints = $addressConstraints->value();
    }

    /**
     * @param UpdateAddress $job
     *
     * @return Address
     */
    public function handle(UpdateAddress $job)
    {
        $attributes = $job->getAddressData();
        $customer = $job->getCustomer();
        $attributes['location'] = new Point($attributes['latitude'], $attributes['longitude']);

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
