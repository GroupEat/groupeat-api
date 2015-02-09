<?php namespace Groupeat\Restaurants\Services;

use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Services\SendMail;

class SendGroupOrderHasBeenJoinedMail {

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
    public function call(Order $order)
    {
        $order->productFormats->load('product.type');

        $customer = $order->customer;
        $deliveryAddress = $order->deliveryAddress;
        $groupOrder = $order->groupOrder;

        $this->mailer->call(
            $groupOrder->restaurant->credentials,
            'restaurants::mails.groupOrderHasBeenJoined',
            'restaurants::groupOrders.joined.subject',
            compact('order', 'customer', 'deliveryAddress', 'groupOrder')
        );
    }

}
