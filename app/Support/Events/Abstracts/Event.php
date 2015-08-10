<?php
namespace Groupeat\Support\Events\Abstracts;

use Illuminate\Queue\SerializesModels;

abstract class Event
{
    use SerializesModels;

    public function __toString()
    {
        return get_class();
    }
}
