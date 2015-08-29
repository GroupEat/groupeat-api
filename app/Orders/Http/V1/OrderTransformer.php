<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Customers\Http\V1\CustomerTransformer;
use Groupeat\Orders\Entities\Order;
use Groupeat\Restaurants\Entities\Product;
use Groupeat\Restaurants\Entities\ProductFormat;
use Groupeat\Restaurants\Http\V1\ProductFormatTransformer;
use Groupeat\Restaurants\Http\V1\ProductTransformer;
use Groupeat\Restaurants\Http\V1\RestaurantTransformer;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['customer', 'groupOrder', 'deliveryAddress', 'products'];

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

    public function includeProducts(Order $order)
    {
        $productFormats = $order->productFormats;
        $productIds = array_unique($productFormats->map(function (ProductFormat $format) {
            return $format->productId;
        })->all());
        $products = Product::whereIn('id', $productIds)->get();

        return $this->collection($products, function (Product $product) use ($productFormats) {
            $productData = (new ProductTransformer)->transform($product);

            $productData['formats'] = $productFormats
                ->filter(function (ProductFormat $format) use ($product) {
                    return $format->productId == $product->id;
                })
                ->map(function (ProductFormat $format) {
                    $formatData = (new ProductFormatTransformer)->transform($format);
                    $formatData['quantity'] = $format->pivot->quantity;

                    return $formatData;
                });

            return $productData;
        });
    }
}
