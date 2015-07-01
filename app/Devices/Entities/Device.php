<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Entities\Traits\HasPosition;

class Device extends Entity
{
    use HasPosition;

    public static function findByUUID($UUID)
    {
        return static::where('UUID', $UUID)->first();
    }

    public function getRules()
    {
        return [
            'customerId' => 'required',
            'UUID' => 'required',
            'notificationToken' => 'required',
            'platformId' => 'required',
            'platformVersion' => 'required',
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
