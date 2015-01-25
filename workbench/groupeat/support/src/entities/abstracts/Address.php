<?php namespace Groupeat\Support\Entities\Abstracts;

use Groupeat\Support\Entities\Entity;
use Treffynnon\Navigator;

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

    /**
     * @param Address $other
     *
     * @return float
     */
    public function distanceInKmsWith(Address $other)
    {
        return Navigator::getDistance(
            $this->latitude,
            $this->longitude,
            $other->latitude,
            $other->longitude
        ) / 1000;
    }

}
