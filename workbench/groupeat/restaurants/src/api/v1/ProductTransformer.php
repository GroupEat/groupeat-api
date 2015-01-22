<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    public function transform(Product $product)
    {
        return [
            'id' => (int) $product->id,
            'type_id' => (int) $product->type_id,
            'name' => $product->name,
            'description' => $product->description,
        ];
    }
}
