<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Restaurants\Migrations\RestaurantAddressesMigration;
use Groupeat\Support\Entities\Entity;

class Address extends Entity {

    protected $fillable = ['street', 'city', 'postcode', 'state', 'country', 'latitude', 'longitude'];


    public function getRules()
    {
        return [
            'restaurant_id' => 'required|integer',
            'street' => 'required',
            'city' => 'required',
            'postcode' => 'required|digits:5',
            'state' => 'required',
            'country' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    protected function getRelatedMigration()
    {
        return new RestaurantAddressesMigration;
    }

}
