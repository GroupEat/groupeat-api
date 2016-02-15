<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Jobs\Abstracts\Job;
use Psr\Log\LoggerInterface;

class CloseGroupOrderAfterFoodrush extends Job
{
    private $groupOrder;

    public function __construct(GroupOrder $groupOrder)
    {
        $this->groupOrder = $groupOrder;
    }

    public function handle(LoggerInterface $logger)
    {
        if (!$this->groupOrder->closedAt) {
            $logger->info($this->groupOrder->toShortString() . ' foodrush has ended');
            $this->groupOrder->close();
        } else {
            $logger->info(
                $this->groupOrder->toShortString() . ' has already been closed at ' . $this->groupOrder->closedAt
            );
        }
    }
}
