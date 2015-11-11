<?php
namespace Groupeat\Support\Console\Abstracts;

use Illuminate\Console\Command as IlluminateCommand;

abstract class Command extends IlluminateCommand
{
    /**
     * @param string $command
     * @param int $timeoutInSeconds null for no timeout
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function process($command, $timeoutInSeconds = null)
    {
        return process($command, $this->output, null, $timeoutInSeconds);
    }

    protected function fail($code = 1)
    {
        return $code;
    }
}
