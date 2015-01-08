<?php namespace Groupeat\Support\Console;

use File;
use Illuminate\Console\Command as IlluminateCommand;

abstract class Command extends IlluminateCommand {

    /**
     * @param $command
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function process($command)
    {
        return processAtProjectRoot($command, $this->output);
    }

}
