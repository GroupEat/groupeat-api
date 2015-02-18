<?php namespace Groupeat\Orders\Api\V1;

use Auth;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Api\V1\Controller;
use Input;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller {

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

        if (Input::json('groupOrderId'))
        {
            $groupOrder = GroupOrder::findOrFail(Input::json('groupOrderId'));

            $order = app('JoinGroupOrderService')->call(
                $groupOrder,
                $customer,
                $productFormats,
                $deliveryAddressData
            );
        }
        else
        {
            $order = app('CreateGroupOrderService')->call(
                $customer,
                $productFormats,
                Input::json()->getInt('foodRushDurationInMinutes'),
                $deliveryAddressData
            );
        }

        return $this->itemResponse($order)->statusCode(Response::HTTP_CREATED);
    }

    private function assertCanBeSeen(Order $order)
    {
        if (!Auth::isSame($order->groupOrder->restaurant))
        {
            Auth::assertSame($order->customer);
        }
    }

}
