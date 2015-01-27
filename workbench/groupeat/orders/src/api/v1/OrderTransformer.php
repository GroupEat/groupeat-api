<?php namespace Groupeat\Orders\Api\V1;

use Groupeat\Orders\Entities\Order;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    public function transform(Order $order)
    {
        return [
            'id' => (int) $order->id,
            'groupOrderId' => $order->groupOrder->id,
        ];
    }

}
