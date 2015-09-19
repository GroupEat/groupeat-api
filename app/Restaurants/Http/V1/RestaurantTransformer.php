<?php
namespace Groupeat\Restaurants\Http\V1;

use Groupeat\Restaurants\Entities\Restaurant;
use League\Fractal\TransformerAbstract;

class RestaurantTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['address', 'categories'];

    public function transform(Restaurant $restaurant)
    {
        return [
            'id' => $restaurant->id,
            'name' => $restaurant->name,
            'email' => $restaurant->email,
            'rating' => $restaurant->rating,
            'phoneNumber' => $restaurant->phoneNumber,
            'minimumGroupOrderPrice' => $restaurant->minimumGroupOrderPrice->getAmount(),
            'deliveryCapacity' => $restaurant->deliveryCapacity,
            'pictureUrl' => $restaurant->pictureUrl,
            'discountPolicy' => $restaurant->discountPolicy,
        ];
    }

    public function includeAddress(Restaurant $restaurant)
    {
        return $this->item($restaurant->address, new AddressTransformer());
    }

    public function includeCategories(Restaurant $restaurant)
    {
        return $this->collection($restaurant->categories, new CategoryTransformer());
    }
}
