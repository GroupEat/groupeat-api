<?php
namespace Groupeat\Customers\Listeners;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenConfirmed;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Groupeat\Support\Services\SendMail;

class SendGroupOrderHasBeenConfirmedMails extends QueuedListener
{
    private $mailer;

    public function __construct(SendMail $mailer)
    {
        $this->mailer = $mailer;
    }

    public function handle(GroupOrderHasBeenConfirmed $groupOrderHasBeenConfirmed)
    {
        $groupOrder = $groupOrderHasBeenConfirmed->getGroupOrder();

        $groupOrder->load([
            'orders.productFormats.product.type',
            'orders.customer.credentials',
            'orders.deliveryAddress',
        ]);

        foreach ($groupOrder->orders as $order) {
            return $this->sendFor($groupOrder, $order);
        }
    }

    private function sendFor(GroupOrder $groupOrder, Order $order)
    {
        $restaurant = $groupOrder->restaurant;
        $deliveryAddress = $order->deliveryAddress;

        if (!$order->isExternal()) {
            $this->mailer->call(
                $order->customer->credentials,
                'customers::orderHasBeenConfirmed',
                'customers::orders.confirmed.subject',
                compact('groupOrder', 'order', 'restaurant', 'deliveryAddress')
            );
        }
    }
}
