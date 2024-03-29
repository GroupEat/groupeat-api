<?php
namespace Groupeat\Orders\Events;

use Groupeat\Orders\Entities\Order;
use Groupeat\Support\Events\Abstracts\Event;

class GroupOrderHasBeenJoined extends Event
{
    private $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function __toString()
    {
        $groupOrder = $this->order->groupOrder;

        return $this->order->customer . ' has joined the ' . $groupOrder->toShortString();
    }
}
