<?php
namespace Groupeat\Restaurants\Entities;

use Groupeat\Support\Entities\Abstracts\Address as AbstractAddress;

class Address extends AbstractAddress
{
    protected $table = 'restaurant_addresses';

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
}
