<?php
namespace Groupeat\Support\Listeners\Abstracts;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

abstract class QueuedListener implements ShouldQueue
{
    use InteractsWithQueue;
}
