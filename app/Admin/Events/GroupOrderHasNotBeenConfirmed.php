<?php
namespace Groupeat\Admin\Events;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Events\Abstracts\Event;

class GroupOrderHasNotBeenConfirmed extends Event
{
    private $groupOrder;

    public function __construct(GroupOrder $groupOrder)
    {
        $this->groupOrder = $groupOrder;
    }

    public function getGroupOrder()
    {
        return $this->groupOrder;
    }

    public function __toString()
    {
        $groupOrderPresenter = $this->groupOrder->getPresenter();
        $restaurant = $this->groupOrder->restaurant;

        return 'The ' . $this->groupOrder->toShortString()
            . ' has been closed at ' . $groupOrderPresenter->closedAtTime
            . ' but has not been confirmed yet by ' . $restaurant->toShortString()
            . ' (' . $restaurant->phoneNumber . ').';
    }
}
