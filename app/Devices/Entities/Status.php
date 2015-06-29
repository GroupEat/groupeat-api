<?php
namespace Groupeat\Devices\Entities;

use Groupeat\Support\Entities\Abstracts\ImmutableDatedEntity;
use Groupeat\Support\Entities\Traits\HasPosition;

class Status extends ImmutableDatedEntity
{
    use HasPosition;

    public function getRules()
    {
        return [
            'deviceId' => 'required',
            'version' => 'required',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ];
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }
}
