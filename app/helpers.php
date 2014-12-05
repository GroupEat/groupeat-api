<?php

use Symfony\Component\Console\Output\BufferedOutput;

// Call an Artisan command and return its output
function artisan($command, $parameters = [], $verbosity = null)
{
    $output = new BufferedOutput($verbosity);

    Artisan::call($command, $parameters, $output);

    return $output->fetch();
}
