<?php namespace Groupeat\Customers\Entities;

use Groupeat\Support\Entities\Entity;

class Address extends Entity {

    public function getRules()
    {
        return [];
    }

    public function customer()
    {
        return $this->belongsTo('Groupeat\Customers\Entities\Customer');
    }

}
