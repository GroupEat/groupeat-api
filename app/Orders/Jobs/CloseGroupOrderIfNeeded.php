<?php
namespace Groupeat\Orders\Jobs;

use Groupeat\Orders\Entities\GroupOrder;
use Groupeat\Support\Jobs\Abstracts\Job;
use Psr\Log\LoggerInterface;

class CloseGroupOrderIfNeeded extends Job
{
    private $groupOrder;

    public function __construct(GroupOrder $groupOrder)
    {
        $this->groupOrder = $groupOrder;
    }

    public function handle(LoggerInterface $logger)
    {
        if (!$this->groupOrder->closedAt) {
            $logger->info('closing '.$this->groupOrder->toShortString());
            $this->groupOrder->close();
        } else {
            $logger->info(
                $this->groupOrder->toShortString() . ' has already been closed at ' . $this->groupOrder->closedAt
            );
        }
    }
}
