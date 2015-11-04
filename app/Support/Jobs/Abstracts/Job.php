<?php
namespace Groupeat\Support\Jobs\Abstracts;

use Groupeat\Support\Values\Abstracts\Activity;
use Illuminate\Queue\SerializesModels;

abstract class Job extends Activity
{
    use SerializesModels;
}
