<?php
namespace Groupeat\Customers\Entities;

use Groupeat\Customers\Migrations\CustomerAddressesMigration;
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
        return $this->belongsTo(Customer::class);
    }

    protected function getRelatedMigration()
    {
        return new CustomerAddressesMigration;
    }
}
