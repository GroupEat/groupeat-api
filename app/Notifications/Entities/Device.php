<?php
namespace Groupeat\Notifications\Entities;

use Groupeat\Support\Entities\Entity;

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
        return $this->hasOne('Groupeat\Customers\Entities\Customer');
    }
}
