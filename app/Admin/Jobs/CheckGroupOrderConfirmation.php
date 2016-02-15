<?php
namespace Groupeat\Admin\Jobs;

use Groupeat\Admin\Events\GroupOrderHasNotBeenConfirmed;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Contracts\Events\Dispatcher;

class CheckGroupOrderConfirmation extends Job
{
    private $groupOrder;

    public function __construct(GroupOrder $groupOrder)
    {
        $this->groupOrder = $groupOrder;
    }

    public function handle(Dispatcher $events)
    {
        if (!$this->groupOrder->isConfirmed()) {
            $events->fire(new GroupOrderHasNotBeenConfirmed($this->groupOrder));
        }
    }
}
