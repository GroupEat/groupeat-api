<?php namespace Groupeat\Restaurants\Api\V1;

use Groupeat\Restaurants\Entities\Restaurant;
use League\Fractal\TransformerAbstract;

class RestaurantTransformer extends TransformerAbstract
{
    public function transform(Restaurant $restaurant)
    {
        return [
            'id' => (int) $restaurant->id,
            'opened' => (bool) $restaurant->isOpened(),
            'name' => $restaurant->name,
            'categories' => $restaurant->categories->lists('id'),
            'longitude' => (float) $restaurant->address->longitude,
            'latitude' => (float) $restaurant->address->latitude,
        ];
    }

}
