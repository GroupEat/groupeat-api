<?php namespace Groupeat\Customers\Entities;

use Groupeat\Customers\Migrations\CustomerAddressesMigration;
use Groupeat\Support\Entities\Entity;

class Address extends Entity {

    protected $fillable = ['street', 'details', 'city', 'postcode', 'state', 'country', 'latitude', 'longitude'];


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

    public function customer()
    {
        return $this->belongsTo('Groupeat\Customers\Entities\Customer');
    }

    protected function getRelatedMigration()
    {
        return new CustomerAddressesMigration;
    }

}
