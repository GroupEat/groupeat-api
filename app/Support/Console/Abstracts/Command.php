<?php
namespace Groupeat\Support\Console\Abstracts;

use Symfony\Component\Process\Process;
use Illuminate\Console\Command as IlluminateCommand;

abstract class Command extends IlluminateCommand
{
    // Set $timeoutInSeconds to 0 for no timeout at all.
    protected function process(string $command, int $timeoutInSeconds = 0): Process
    {
        return process($command, $this->output, '', $timeoutInSeconds);
    }

    protected function fail($code = 1)
    {
        return $code;
    }
}
