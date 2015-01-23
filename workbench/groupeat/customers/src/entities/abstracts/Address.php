<?php namespace Groupeat\Customers\Entities\Abstracts;

use Groupeat\Support\Entities\Entity;

abstract class Address extends Entity {

    protected $fillable = ['street', 'details', 'city', 'postcode', 'state', 'country', 'latitude', 'longitude'];


    public function getRules()
    {
        return [
            'street' => 'required',
            'city' => 'required',
            'postcode' => 'required|digits:5',
            'state' => 'required',
            'country' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

}
