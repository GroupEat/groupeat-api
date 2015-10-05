<?php
namespace Groupeat\Support\Queue;

use Carbon\Carbon;
use Illuminate\Queue\DatabaseQueue as IlluminateDatabaseQueue;

class DatabaseQueue extends IlluminateDatabaseQueue
{
    protected function getTime()
    {
        return Carbon::now()->timestamp;
    }
}
