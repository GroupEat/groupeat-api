<?php
namespace Groupeat\Orders\Api\V1;

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
            'id' => $groupOrder->id,
            'joinable' => $groupOrder->isJoinable(),
            'discountRate' => $groupOrder->discountRate->toPercentage(),
            'createdAt' => (string) $groupOrder->created_at,
            'remainingCapacity' => $groupOrder->computeRemainingCapacity(),
            'completedAt' => $groupOrder->completed_at ? (string) $groupOrder->completed_at : null,
            'endingAt' => (string) $groupOrder->ending_at,
            'confirmed' => !is_null($groupOrder->confirmed_at),
            'preparedAt' => $groupOrder->prepared_at ? (string) $groupOrder->prepared_at : null,
        ];
    }

    public function includeRestaurant(GroupOrder $groupOrder)
    {
        return $this->item($groupOrder->restaurant, new RestaurantTransformer);
    }
}
