<?php namespace Groupeat\Orders\Api\V1;

use Groupeat\Orders\Entities\GroupOrder;
use League\Fractal\TransformerAbstract;

class GroupOrderTransformer extends TransformerAbstract
{
    public function transform(GroupOrder $groupOrder)
    {
        $restaurant = $groupOrder->restaurant;

        return [
            'id' => (int) $groupOrder->id,
            'opened' => (bool) $groupOrder->isOpened(),
            'reduction' => (float) $groupOrder->reduction,
            'restaurant' => [
                'id' => (int) $restaurant->id,
                'name' => $restaurant->name,
                'categories' => $restaurant->categories->lists('id'),
            ],
            'createdAt' => (string) $groupOrder->created_at,
            'endingAt' => (string) $groupOrder->created_at,
        ];
    }

}
