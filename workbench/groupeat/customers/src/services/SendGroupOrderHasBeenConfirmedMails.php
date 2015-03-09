<?php
namespace Groupeat\Customers\Services;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Restaurants\Entities\Restaurant;
use Groupeat\Support\Services\SendMail;

class SendGroupOrderHasBeenConfirmedMails
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
     * @param GroupOrder $groupOrder
     */
    public function call(GroupOrder $groupOrder)
    {
        $groupOrder->load(
            [
            'orders.productFormats.product.type',
            'orders.customer.credentials',
            'orders.deliveryAddress',
            ]
        );

        foreach ($groupOrder->orders as $order) {
            return $this->sendFor($groupOrder, $order);
        }
    }

    private function sendFor(GroupOrder $groupOrder, Order $order)
    {
        $restaurant = $groupOrder->restaurant;
        $deliveryAddress = $order->deliveryAddress;

        $this->mailer->call(
            $order->customer->credentials,
            'customers::mails.orderHasBeenConfirmed',
            'customers::orders.confirmed.subject',
            compact('groupOrder', 'order', 'restaurant', 'deliveryAddress')
        );
    }
}
