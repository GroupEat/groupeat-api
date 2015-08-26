<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Customers\Http\V1\CustomerTransformer;
use Groupeat\Orders\Entities\Order;
use Groupeat\Restaurants\Http\V1\ProductFormatTransformer;
use Groupeat\Restaurants\Http\V1\RestaurantTransformer;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['customer', 'groupOrder', 'deliveryAddress', 'productFormats'];

    public function transform(Order $order)
    {
        return [
            'id' => $order->id,
            'rawPrice' => $order->rawPrice->getAmount(),
            'discountedPrice' => $order->discountedPrice->getAmount(),
            'createdAt' => (string) $order->createdAt,
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

    public function includeDeliveryAddress(Order $order)
    {
        return $this->item($order->deliveryAddress, new DeliveryAddressTransformer);
    }

    public function includeProductFormats(Order $order)
    {
        return $this->collection($order->productFormats, function ($productFormat) {
            $data = (new ProductFormatTransformer)->transform($productFormat);

            $data['amount'] = $productFormat->pivot->amount;

            return $data;
        });
    }
}
