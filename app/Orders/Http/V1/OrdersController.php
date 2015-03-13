<?php
namespace Groupeat\Orders\Http\V1;

use Auth;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Http\V1\Abstracts\Controller;
use Input;
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
        $customer = Auth::customer();
        $productFormats = new ProductFormats(Input::json('productFormats'));
        $deliveryAddressData = Input::json()->all();
        $comment = Input::json('comment');

        if (Input::json('groupOrderId')) {
            $groupOrder = GroupOrder::findOrFail(Input::json('groupOrderId'));

            $order = app('JoinGroupOrderService')->call(
                $groupOrder,
                $customer,
                $productFormats,
                $deliveryAddressData,
                $comment
            );
        } else {
            $order = app('CreateGroupOrderService')->call(
                $customer,
                $productFormats,
                Input::json()->getInt('foodRushDurationInMinutes'),
                $deliveryAddressData,
                $comment
            );
        }

        $this->statusCode = Response::HTTP_CREATED;

        return $this->itemResponse($order);
    }

    private function assertCanBeSeen(Order $order)
    {
        if (!Auth::isSame($order->groupOrder->restaurant)) {
            Auth::assertSame($order->customer);
        }
    }
}
