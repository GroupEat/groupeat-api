<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Customers\Entities\Customer;
use Groupeat\Support\Entities\Abstracts\Entity;
use Groupeat\Support\Exceptions\NotFound;

class Device extends Entity
{
    public static function findByUUIDorFail($UUID)
    {
        $device = static::findByUUID($UUID);

        if (!$device) {
            throw new NotFound(
                'deviceNotFound',
                "No device with UUID $UUID"
            );
        }

        return $device;
    }

    public static function findByUUID($UUID)
    {
        return static::where('UUID', $UUID)->first();
    }

    public function getRules()
    {
        return [
            'customerId' => 'required',
            'UUID' => 'required',
            'platformId' => 'required',
            'platformVersion' => 'required',
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

    public function locations()
    {
        return $this->hasMany(DeviceLocation::class);
    }
}
