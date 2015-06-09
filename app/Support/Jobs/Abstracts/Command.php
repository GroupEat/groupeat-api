<?php
namespace Groupeat\Support\Jobs\Abstracts;

use Illuminate\Queue\SerializesModels;

abstract class Command
{
    use SerializesModels;
}
