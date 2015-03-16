<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Orders\Commands\CreateGroupOrder;
use Groupeat\Orders\Commands\JoinGroupOrder;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    public function show(Order $order)
    {
        $this->assertCanBeSeen($order);

        return $this->itemResponse($order);
    }

    public function showDeliveryAddress(Order $order)
    {
        $this->assertCanBeSeen($order);

        return $this->itemResponse($order->deliveryAddress);
    }

    public function place()
    {
        $customer = $this->auth->customer();
        $productFormats = $this->json('productFormats');
        $deliveryAddressData = $this->json()->all();
        $comment = $this->json('comment');

        if ($this->json('groupOrderId')) {
            $order = $this->dispatch(new JoinGroupOrder(
                GroupOrder::findOrFail($this->json('groupOrderId')),
                $customer,
                $productFormats,
                $deliveryAddressData,
                $comment
            ));
        } else {
            $order = $this->dispatch(new CreateGroupOrder(
                $this->json()->getInt('foodRushDurationInMinutes'),
                $customer,
                $productFormats,
                $deliveryAddressData,
                $comment
            ));
        }

        $this->statusCode = Response::HTTP_CREATED;

        return $this->itemResponse($order);
    }

    private function assertCanBeSeen(Order $order)
    {
        if (!$this->auth->isSame($order->groupOrder->restaurant)) {
            $this->auth->assertSame($order->customer);
        }
    }
}
