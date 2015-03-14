<?php
namespace Groupeat\Support\Events\Abstracts;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;
}
