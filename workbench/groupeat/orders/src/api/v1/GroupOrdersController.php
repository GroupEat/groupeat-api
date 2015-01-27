<?php namespace Groupeat\Orders\Api\V1;

use App;
use Auth;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Support\ProductFormats;
use Groupeat\Support\Api\V1\Controller;
use Input;
use Symfony\Component\HttpFoundation\Response;

class GroupOrdersController extends Controller {

    public function placeOrder()
    {
        $customer = Auth::customer();
        $productFormats = ProductFormats::fromJSON(Input::get('productFormats'));
        $deliveryAddressData = Input::only((new DeliveryAddress())->getFillable());

        if (Input::has('groupOrderId'))
        {
            $groupOrder = GroupOrder::findOrFail(Input::get('groupOrderId'));

            $order = App::make('JoinGroupOrderService')->call(
                $groupOrder,
                $customer,
                $productFormats,
                $deliveryAddressData
            );
        }
        else
        {
            $order = App::make('CreateGroupOrderService')->call(
                $customer,
                $productFormats,
                (int) Input::get('foodRushDurationInMinutes'),
                $deliveryAddressData
            );
        }

        return $this->itemResponse($order)->statusCode(Response::HTTP_CREATED);
    }

}
