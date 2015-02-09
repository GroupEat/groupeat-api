<?php namespace Groupeat\Support\Api\V1\Abstracts;

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
            'postcode' => (int) $address->postcode,
            'state' => $address->state,
            'country' => $address->country,
            'latitude' => (float) $address->latitude,
            'longitude' => (float) $address->longitude,
        ];
    }

}