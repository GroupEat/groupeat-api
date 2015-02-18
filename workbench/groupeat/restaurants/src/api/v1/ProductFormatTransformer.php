<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\ProductFormat;
use League\Fractal\TransformerAbstract;

class ProductFormatTransformer extends TransformerAbstract
{
    public function transform(ProductFormat $format)
    {
        return [
            'id' => $format->id,
            'name' => $format->name,
            'price' => $format->price->getAmount(),
        ];
    }

}
