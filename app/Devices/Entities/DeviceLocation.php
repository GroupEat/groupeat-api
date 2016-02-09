<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Support\Entities\Abstracts\ImmutableDatedEntity;
use Groupeat\Support\Entities\Traits\HasLocation;
use Phaza\LaravelPostgis\Geometries\Point;

class DeviceLocation extends ImmutableDatedEntity
{
    use HasLocation;

    public static function createFromDeviceAndLocation(Device $device, Point $location)
    {
        $deviceLocation = new static;
        $deviceLocation->device()->associate($device);
        $deviceLocation->location = $location;
        $deviceLocation->save();

        return $deviceLocation;
    }

    public function getRules()
    {
        return [
            'deviceId' => 'required',
            'location' => 'required',
        ];
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
