<?php
namespace Groupeat\Restaurants\Http\V1;

use Groupeat\Restaurants\Entities\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $foodType)
    {
        return [
            'id' => $foodType->id,
            'label' => $foodType->label,
        ];
    }
}
