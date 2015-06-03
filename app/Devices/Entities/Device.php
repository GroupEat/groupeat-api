<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Entities\Abstracts\Entity;

class Device extends Entity
{
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
            'model' => 'required',
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
