<?php
namespace Groupeat\Support\Listeners\Abstracts;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

abstract class QueuedHandler implements ShouldQueue
{
    use InteractsWithQueue;
}
