<?php
namespace Groupeat\Admin\Jobs;

use Groupeat\Admin\Events\GroupOrderHasNotBeenConfirmed;
use Illuminate\Contracts\Events\Dispatcher;

class CheckGroupOrderConfirmationHandler
{
    private $events;
    private $maxConfirmationDurationInMinutes;

    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    public function handle(CheckGroupOrderConfirmation $job)
    {
        $groupOrder = $job->getGroupOrder();

        if (!$groupOrder->isConfirmed()) {
            $this->events->fire(new GroupOrderHasNotBeenConfirmed($groupOrder));
        }
    }
}
