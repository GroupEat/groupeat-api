<?php
namespace Groupeat\Restaurants\Listeners;

use Groupeat\Orders\Entities\Order;
use Groupeat\Orders\Events\GroupOrderHasBeenCreated;
use Groupeat\Orders\Events\GroupOrderHasBeenJoined;
use Groupeat\Support\Listeners\Abstracts\QueuedListener;
use Groupeat\Support\Services\SendMail;

class SendOrderHasBeenPlacedMail extends QueuedListener
{
    private $mailer;

    public function __construct(SendMail $mailer)
    {
        $this->mailer = $mailer;
    }

    public function created(GroupOrderHasBeenCreated $groupOrderHasBeenCreated)
    {
        $this->call($groupOrderHasBeenCreated->getOrder(), 'created');
    }

    public function joined(GroupOrderHasBeenJoined $groupOrderHasBeenJoined)
    {
        $this->call($groupOrderHasBeenJoined->getOrder(), 'joined');
    }

    private function call(Order $order, $action)
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
