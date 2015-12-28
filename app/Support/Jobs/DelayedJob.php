<?php
namespace Groupeat\Support\Jobs;

use Carbon\Carbon;
use Groupeat\Support\Jobs\Abstracts\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class DelayedJob extends Job implements SelfHandling, ShouldQueue
{
    use Queueable;

    private $job;

    public function __construct(Job $job, Carbon $handleAt)
    {
        $this->job = $job;
        $this->delay = $handleAt->diffInSeconds();
    }

    public function getJob()
    {
        return $this->job;
    }

    public function handle(Dispatcher $dispatcher)
    {
        $dispatcher->dispatchNow($this->job);
    }
}
