<?php
namespace Groupeat\Admin\Jobs;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Jobs\Abstracts\Job;

class CheckGroupOrderConfirmation extends Job
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
