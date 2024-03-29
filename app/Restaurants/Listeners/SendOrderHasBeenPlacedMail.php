<?php
namespace Groupeat\Restaurants\Listeners;

use Groupeat\Mailing\Services\SendMail;
use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;

class SendOrderHasBeenPlacedMail extends QueuedListener
{
    private $mailer;

    public function __construct(SendMail $mailer)
    {
        $this->mailer = $mailer;
    }

    public function created(GroupOrderHasBeenCreated $event)
    {
        $this->call($event->getOrder(), 'created');
    }

    public function joined(GroupOrderHasBeenJoined $event)
    {
        $this->call($event->getOrder(), 'joined');
    }

    private function call(Order $order, string $action)
    {
        $order->productFormats->load('product.type');

        $customer = $order->customer;
        $deliveryAddress = $order->deliveryAddress;
        $groupOrder = $order->groupOrder;

        $this->mailer->call(
            $groupOrder->restaurant->credentials,
            'restaurants::orderHasBeenPlaced',
            "restaurants::groupOrders.$action.subject",
            compact('order', 'customer', 'deliveryAddress', 'groupOrder', 'action')
        );
    }
}
