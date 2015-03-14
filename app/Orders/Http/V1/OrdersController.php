<?php
namespace Groupeat\Orders\Http\V1;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Services\CreateGroupOrder;
use Groupeat\Orders\Services\JoinGroupOrder;
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

    public function place(JoinGroupOrder $joinGroupOrder, CreateGroupOrder $createGroupOrder)
    {
        $customer = $this->auth->customer();
        $productFormats = new ProductFormats($this->json('productFormats'));
        $deliveryAddressData = $this->json()->all();
        $comment = $this->json('comment');

        if ($this->json('groupOrderId')) {
            $groupOrder = GroupOrder::findOrFail($this->json('groupOrderId'));

            $order = $joinGroupOrder->call(
                $groupOrder,
                $customer,
                $productFormats,
                $deliveryAddressData,
                $comment
            );
        } else {
            $order = $createGroupOrder->call(
                $customer,
                $productFormats,
                $this->json()->getInt('foodRushDurationInMinutes'),
                $deliveryAddressData,
                $comment
            );
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
