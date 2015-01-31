<?php namespace Groupeat\Restaurants\Entities;

use Groupeat\Restaurants\Migrations\RestaurantAddressesMigration;
use Groupeat\Support\Entities\Abstracts\Address as AbstractAddress;

class Address extends AbstractAddress {

    public function getRules()
    {
        $rules = parent::getRules();

        $rules['restaurant_id'] = 'required|integer';

        return $rules;
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
