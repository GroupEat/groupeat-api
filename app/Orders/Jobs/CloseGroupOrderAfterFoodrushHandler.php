<?php
namespace Groupeat\Orders\Jobs;

use Psr\Log\LoggerInterface;

class CloseGroupOrderAfterFoodrushHandler
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(CloseGroupOrderAfterFoodrush $job)
    {
        $groupOrder = $job->getGroupOrder();

        if (!$groupOrder->closedAt) {
            $this->logger->info($groupOrder->toShortString() . ' foodrush has ended');
            $groupOrder->close();
        } else {
            $this->logger->info($groupOrder->toShortString() . ' has already been closed at ' . $groupOrder->closedAt);
        }
    }
}
