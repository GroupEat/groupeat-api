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
            'joinable' => (bool) $groupOrder->isJoinable(),
            'discountRate' => (int) $groupOrder->discountRate->toPercentage(),
            'createdAt' => (string) $groupOrder->created_at,
            'remainingCapacity' => (int) $groupOrder->computeRemainingCapacity(),
            'endingAt' => (string) $groupOrder->ending_at,
            'confirmed' => (bool) !is_null($groupOrder->confirmed_at),
            'preparedAt' => $groupOrder->prepared_at,
        ];
    }

    public function includeRestaurant(GroupOrder $groupOrder)
    {
        return $this->item($groupOrder->restaurant, new RestaurantTransformer);
    }

}
