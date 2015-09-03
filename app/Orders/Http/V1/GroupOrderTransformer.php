<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Restaurants\Http\V1\RestaurantTransformer;
use League\Fractal\TransformerAbstract;

class GroupOrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['restaurant', 'orders'];

    public function transform(GroupOrder $groupOrder)
    {
        $restaurant = $groupOrder->restaurant;

        return [
            'id' => $groupOrder->id,
            'joinable' => $groupOrder->isJoinable(),
            'totalRawPrice' => $groupOrder->totalRawPrice->getAmount(),
            'discountRate' => $groupOrder->discountRate->toPercentage(),
            'createdAt' => (string) $groupOrder->createdAt,
            'remainingCapacity' => $groupOrder->computeRemainingCapacity(),
            'closedAt' => $groupOrder->closedAt ? (string) $groupOrder->closedAt : null,
            'endingAt' => (string) $groupOrder->endingAt,
            'confirmed' => !is_null($groupOrder->confirmedAt),
            'preparedAt' => $groupOrder->preparedAt ? (string) $groupOrder->preparedAt : null,
        ];
    }

    public function includeRestaurant(GroupOrder $groupOrder)
    {
        return $this->item($groupOrder->restaurant, new RestaurantTransformer);
    }

    public function includeOrders(GroupOrder $groupOrder)
    {
        return $this->collection($groupOrder->orders, new OrderTransformer);
    }
}
