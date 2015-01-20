<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\Address;
use League\Fractal\TransformerAbstract;

class AddressTransformer extends TransformerAbstract
{
    public function transform(Address $address)
    {
        return [
            'street' => $address->street,
            'city' => $address->city,
            'postcode' => $address->postcode,
            'state' => $address->state,
            'country' => $address->country,
        ];
    }
}
