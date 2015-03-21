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
            'hardwareId' => 'required',
            'notificationToken' => 'required',
            'operating_system_id' => 'required|numeric',
            'operatingSystemVersion' => 'required',
            'model' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function operatingSystem()
    {
        return $this->belongsTo(OperatingSystem::class);
    }
}
