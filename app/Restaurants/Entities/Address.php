<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Restaurants\Migrations\RestaurantAddressesMigration;
use Groupeat\Support\Entities\Abstracts\Address as AbstractAddress;

class Address extends AbstractAddress
{
    public function getRules()
    {
        $rules = parent::getRules();

        $rules['restaurantId'] = 'required|integer';

        return $rules;
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    protected function getRelatedMigration()
    {
        return new RestaurantAddressesMigration;
    }
}
