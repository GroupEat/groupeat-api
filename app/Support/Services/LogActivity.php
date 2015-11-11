<?php
namespace Groupeat\Support\Services;

use Groupeat\Support\Events\Abstracts\Event;
use Groupeat\Support\Jobs\Abstracts\Job;
use Groupeat\Support\Jobs\DelayedJob;
use Groupeat\Support\Values\Abstracts\Activity;
use Psr\Log\LoggerInterface;

class LogActivity
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function logEvent($event)
    {
        if ($event instanceof Event) {
            $this->log($event);
        }
    }

    public function handle(Job $job, $next)
    {
        if (!($job instanceof DelayedJob)) {
            $this->log($job);
        }

        return $next($job);
    }

    private function log(Activity $activity)
    {
        $this->logger->info(get_class($activity).' '.$activity->toJson());
    }
}
