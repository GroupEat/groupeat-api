<?php
namespace Groupeat\Support\Commands\Abstracts;

use Illuminate\Queue\SerializesModels;

abstract class Command
{
    use SerializesModels;
}
