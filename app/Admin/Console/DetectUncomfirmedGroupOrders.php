<?php
namespace Groupeat\Admin\Console;

use Carbon\Carbon;
use Groupeat\Admin\Events\GroupOrderHasNotBeenConfirmed;
use Groupeat\Admin\Values\MaxConfirmationDurationInMinutes;
use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Console\Abstracts\Command;
use Illuminate\Contracts\Events\Dispatcher;

class DetectUncomfirmedGroupOrders extends Command
{
    protected $signature = 'group-orders:detect-uncomfirmed';
    protected $description = "Detect group orders that should have been confirmed and fire events if found";

    private $events;
    private $maxConfirmationDurationInMinutes;

    public function __construct(
        Dispatcher $events,
        MaxConfirmationDurationInMinutes $maxConfirmationDurationInMinutes
    ) {
        parent::__construct();

        $this->events = $events;
        $this->maxConfirmationDurationInMinutes = $maxConfirmationDurationInMinutes;
    }

    public function handle()
    {
        GroupOrder::unconfirmed($this->maxConfirmationDurationInMinutes)
            ->get()
            ->each(function (GroupOrder $groupOrder) {
                $this->comment($groupOrder->toShortString() . ' is unconfirmed');
                $this->events->fire(new GroupOrderHasNotBeenConfirmed($groupOrder));
            });
    }
}
