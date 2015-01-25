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

    protected function getRelatedMigration()
    {
        return new RestaurantAddressesMigration;
    }

}
