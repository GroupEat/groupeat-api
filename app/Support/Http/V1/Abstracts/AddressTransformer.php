<?php
namespace Groupeat\Support\Http\V1\Abstracts;

use Groupeat\Support\Entities\Abstracts\Address;
use League\Fractal\TransformerAbstract;

abstract class AddressTransformer extends TransformerAbstract
{
    public function transform(Address $address)
    {
        return [
            'street' => $address->street,
            'details' => $address->details,
            'city' => $address->city,
            'postcode' => $address->postcode,
            'state' => $address->state,
            'country' => $address->country,
            'latitude' => $address->location->getLat(),
            'longitude' => $address->location->getLng(),
        ];
    }
}
