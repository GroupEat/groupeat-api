<?php
namespace Groupeat\Restaurants\Http\V1;

use Groupeat\Restaurants\Entities\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'label' => $category->label,
        ];
    }
}
