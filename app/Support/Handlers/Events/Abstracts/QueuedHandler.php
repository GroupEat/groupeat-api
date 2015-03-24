<?php
namespace Groupeat\Support\Handlers\Events\Abstracts;

use Illuminate\Contracts\Queue\ShouldBeQueued;
use Illuminate\Queue\InteractsWithQueue;

abstract class QueuedHandler implements ShouldBeQueued
{
    use InteractsWithQueue;
}
