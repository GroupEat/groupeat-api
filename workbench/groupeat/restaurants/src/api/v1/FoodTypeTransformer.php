<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\FoodType;
use League\Fractal\TransformerAbstract;

class FoodTypeTransformer extends TransformerAbstract
{
    public function transform(FoodType $foodType)
    {
        return [
            'id' => (int) $foodType->id,
            'name' => $foodType->name,
        ];
    }
}