<?php namespace Groupeat\Orders\Api\V1;

use Groupeat\Customers\Api\V1\CustomerTransformer;
use Groupeat\Orders\Entities\Order;
use Groupeat\Restaurants\Api\V1\RestaurantTransformer;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['customer', 'groupOrder', 'restaurant', 'deliveryAddress'];


    public function transform(Order $order)
    {
        return [
            'id' => $order->id,
            'rawPrice' => $order->rawPrice->getAmount(),
            'discountedPrice' => $order->discountedPrice->getAmount(),
            'createdAt' => (string) $order->created_at,
            'comment' => $order->comment,
        ];
    }

    public function includeCustomer(Order $order)
    {
        return $this->item($order->customer, new CustomerTransformer);
    }

    public function includeGroupOrder(Order $order)
    {
        return $this->item($order->groupOrder, new GroupOrderTransformer);
    }

    public function includeRestaurant(Order $order)
    {
        return $this->item($order->groupOrder->restaurant, new RestaurantTransformer);
    }

    public function includeDeliveryAddress(Order $order)
    {
        return $this->item($order->deliveryAddress, new DeliveryAddressTransformer);
    }

}
