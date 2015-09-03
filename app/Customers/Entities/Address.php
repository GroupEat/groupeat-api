<?php
namespace Groupeat\Customers\Entities;

use Groupeat\Support\Entities\Abstracts\Address as AbstractAddress;

class Address extends AbstractAddress
{
    protected $table = 'customer_addresses';

    public function getRules()
    {
        $rules = parent::getRules();

        $rules['customerId'] = 'required|integer';

        return $rules;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
