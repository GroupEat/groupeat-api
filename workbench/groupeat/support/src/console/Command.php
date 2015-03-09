<?php
namespace Groupeat\Support\Console;

use Illuminate\Console\Command as IlluminateCommand;

abstract class Command extends IlluminateCommand
{
    /**
     * @param $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function process($command, $timeout = null)
    {
        return process($command, $this->output, null, $timeout);
    }
}
