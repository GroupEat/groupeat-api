<?php namespace Groupeat\Customers\Api\V1;

use Groupeat\Customers\Entities\Address;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    public function transform(Address $address)
    {
        return [
            'street' => $address->street,
            'details' => $address->details,
            'city' => $address->city,
            'postcode' => (int) $address->postcode,
            'state' => $address->state,
            'country' => $address->country,
            'latitude' => (float) $address->latitude,
            'longitude' => (float) $address->longitude,
        ];
    }
}
