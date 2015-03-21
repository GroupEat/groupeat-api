<?php
namespace Groupeat\Devices\Http\V1;

use Groupeat\Devices\Entities\OperatingSystem;
use League\Fractal\TransformerAbstract;

class OperatingSystemTransformer extends TransformerAbstract
{
    public function transform(OperatingSystem $operatingSystem)
    {
        return [
            'id' => $operatingSystem->id,
            'label' => $operatingSystem->label,
        ];
    }
}
