<?php
namespace Groupeat\Restaurants\Http\V1;

use Groupeat\Restaurants\Entities\FoodTag;
use League\Fractal\TransformerAbstract;

class FoodTagTransformer extends TransformerAbstract
{
    public function transform(FoodTag $foodTag)
    {
        return [
            'id' => $foodTag->id,
            'label' => $foodTag->label,
        ];
    }
}
