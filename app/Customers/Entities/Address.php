<?php
namespace Groupeat\Customers\Entities;

use CustomerAddressesMigration;
use Groupeat\Support\Entities\Abstracts\Address as AbstractAddress;

class Address extends AbstractAddress
{
    public function getRules()
    {
        $rules = parent::getRules();

        $rules['customer_id'] = 'required|integer';

        return $rules;
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