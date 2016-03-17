<?php
namespace Groupeat\Admin\Events;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Events\Abstracts\Event;

class GroupOrderHasBeenMadeUp extends Event
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
}
