<?php
namespace Groupeat\Support\Jobs\Abstracts;

use Illuminate\Queue\SerializesModels;

abstract class Job
{
    use SerializesModels;
}
