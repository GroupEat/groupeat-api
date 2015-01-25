<?php namespace Groupeat\Orders\Api\V1;

use App;
use Auth;
use Groupeat\Orders\Entities\DeliveryAddress;
use Groupeat\Support\Api\V1\Controller;
use Input;

class OrdersController extends Controller {

    public function placeOrder()
    {
        App::make('PlaceOrderService')->call(
            Auth::customer(),
            decodeJSON(Input::get('productFormats')),
            (int) Input::get('foodRushDurationInMinutes'),
            Input::only((new DeliveryAddress())->getFillable())
        );

        return $this->response->created();
    }

}
