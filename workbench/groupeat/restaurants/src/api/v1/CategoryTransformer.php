<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $foodType)
    {
        return [
            'id' => (int) $foodType->id,
            'label' => $foodType->label,
        ];
    }

}
