<?php
namespace Groupeat\Support\Handlers\Events\Abstracts;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

abstract class QueuedHandler implements ShouldQueue
{
    use InteractsWithQueue;
}
