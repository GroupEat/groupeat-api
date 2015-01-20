<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Restaurants\Migrations\RestaurantAddressesMigration;
use Groupeat\Support\Entities\Entity;

class Address extends Entity {

    protected $fillable = ['street', 'city', 'postcode', 'state', 'country', 'latitude', 'longitude'];


    public function getRules()
    {
        return [
            'customer_id' => 'required|integer',
            'street' => 'required',
            'city' => 'required',
            'postcode' => 'required',
            'state' => 'required',
            'country' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function restaurant()
    {
        return $this->belongsTo('Groupeat\Restaurants\Entities\Restaurant');
    }

    protected function getRelatedMigration()
    {
        return new RestaurantAddressesMigration;
    }

}
