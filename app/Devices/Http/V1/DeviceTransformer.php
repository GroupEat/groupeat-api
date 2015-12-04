<?php
namespace Groupeat\Devices\Http\V1;

use Groupeat\Devices\Entities\Device;
use League\Fractal\TransformerAbstract;

class DeviceTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['platform'];

    public function transform(Device $device)
    {
        return [
            'id' => $device->id,
            'UUID' => $device->UUID,
            'model' => $device->model,
            'createdAt' => (string) $device->createdAt,
            'updatedAt' => (string) $device->updatedAt,
        ];
    }

    public function includePlatform(Device $device)
    {
        return $this->item($device->platform, new PlatformTransformer);
    }
}
