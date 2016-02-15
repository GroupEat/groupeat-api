<?php
namespace Groupeat\Restaurants\Http\V1;

use Groupeat\Restaurants\Entities\Category;
use Groupeat\Restaurants\Entities\Promotion;
use League\Fractal\TransformerAbstract;

class PromotionTransformer extends TransformerAbstract
{
    public function transform(Promotion $promotion)
    {
        return [
            'id' => $promotion->id,
            'restaurantId' => $promotion->restaurantId,
            'rawPriceThreshold' => $promotion->rawPriceThreshold->getAmount(),
            'beneficiaryCount' => $promotion->beneficiaryCount,
            'name' => $promotion->name,
        ];
    }
}
