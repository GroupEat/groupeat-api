<?php
namespace Groupeat\Support\Events\Abstracts;

use Groupeat\Support\Values\Abstracts\Activity;
use Illuminate\Queue\SerializesModels;

abstract class Event extends Activity
{
    use SerializesModels;
}
