<?php
namespace Groupeat\Restaurants\Http\V1;

use Groupeat\Restaurants\Entities\Product;
use League\Fractal\TransformerAbstract;

class ProductTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['formats', 'type', 'tags'];

    public function transform(Product $product)
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
        ];
    }

    public function includeFormats(Product $product)
    {
        return $this->collection($product->formats, new ProductFormatTransformer);
    }

    public function includeType(Product $product)
    {
        return $this->item($product->type, new FoodTypeTransformer);
    }

    public function includeTags(Product $product)
    {
        return $this->collection($product->tags, new FoodTagTransformer);
    }
}
