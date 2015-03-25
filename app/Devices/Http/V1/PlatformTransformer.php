<?php
namespace Groupeat\Devices\Http\V1;

use Groupeat\Devices\Entities\Platform;
use League\Fractal\TransformerAbstract;

class PlatformTransformer extends TransformerAbstract
{
    public function transform(Platform $platform)
    {
        return [
            'id' => $platform->id,
            'label' => $platform->label,
        ];
    }
}
