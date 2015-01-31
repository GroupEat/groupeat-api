<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\Restaurant;
use League\Fractal\TransformerAbstract;

class RestaurantTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['address', 'categories'];

    public function transform(Restaurant $restaurant)
    {
        return [
            'id' => (int) $restaurant->id,
            'opened' => (bool) $restaurant->isOpened(),
            'name' => $restaurant->name,
            'phoneNumber' => $restaurant->phoneNumber,
            'minimumOrderPrice' => (float) $restaurant->minimumOrderPrice,
            'deliveryCapacity' => (int) $restaurant->deliveryCapacity,
            'reductionPrices' => $restaurant->reductionPrices,
        ];
    }

    public function includeAddress(Restaurant $restaurant)
    {
        return $this->item($restaurant->address, new AddressTransformer);
    }

    public function includeCategories(Restaurant $restaurant)
    {
        return $this->collection($restaurant->categories, new CategoryTransformer);
    }

}
