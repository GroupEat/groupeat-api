<?php
namespace Groupeat\Restaurants\Services;

use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Services\SendMail;

class SendOrderHasBeenPlacedMail
{
    /**
     * @var SendMail
     */
    private $mailer;

    public function __construct(SendMail $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param Order $order
     */
    public function created(Order $order)
    {
        $this->call($order, 'created');
    }

    /**
     * @param Order $order
     */
    public function joined(Order $order)
    {
        $this->call($order, 'joined');
    }

    /**
     * @param Order  $order
     * @param string $action
     */
    private function call(Order $order, $action)
    {
        $order->productFormats->load('product.type');

        $customer = $order->customer;
        $deliveryAddress = $order->deliveryAddress;
        $groupOrder = $order->groupOrder;

        $this->mailer->call(
            $groupOrder->restaurant->credentials,
            'restaurants::mails.orderHasBeenPlaced',
            "restaurants::groupOrders.$action.subject",
            compact('order', 'customer', 'deliveryAddress', 'groupOrder', 'action')
        );
    }
}
