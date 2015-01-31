<?php namespace Groupeat\Orders\Api\V1;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Restaurants\Api\V1\RestaurantTransformer;
use League\Fractal\TransformerAbstract;

class GroupOrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['restaurant'];


    public function transform(GroupOrder $groupOrder)
    {
        $restaurant = $groupOrder->restaurant;

        return [
            'id' => (int) $groupOrder->id,
            'opened' => (bool) $groupOrder->isOpened(),
            'reduction' => (float) $groupOrder->reduction,
            'createdAt' => (string) $groupOrder->created_at,
            'endingAt' => (string) $groupOrder->created_at,
        ];
    }

    public function includeRestaurant(GroupOrder $groupOrder)
    {
        return $this->item($groupOrder->restaurant, new RestaurantTransformer);
    }

}
