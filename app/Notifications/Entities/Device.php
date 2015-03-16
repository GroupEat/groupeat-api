<?php
namespace Groupeat\Notifications\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Entities\Abstracts\Entity;

class Device extends Entity
{
    public function getRules()
    {
        return [
            'customer_id' => 'required',
            'device_id' => 'required',
        ];
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }
}
