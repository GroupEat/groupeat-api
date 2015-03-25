<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Entities\Abstracts\Entity;

class Device extends Entity
{
    public function getRules()
    {
        return [
            'customer_id' => 'required',
            'UUID' => 'required',
            'notificationToken' => 'required',
            'platform_id' => 'required',
            'version' => 'required',
            'model' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }
}
