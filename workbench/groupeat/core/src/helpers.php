<?php

if (!function_exists('artisan'))
{
    /**
     * Call an Artisan command and return its output
     *
     * @param       $command Command name (like groupeat:push)
     * @param array $parameters Command options
     * @param null  $verbosity
     *
     * @return string Command output
     */
    function artisan($command, $parameters = [], $verbosity = null)
    {
        $output = new \Symfony\Component\Console\Output\BufferedOutput($verbosity);
        \Illuminate\Support\Facades\Artisan::call($command, $parameters, $output);

        return $output->fetch();
    }
}

if (!function_exists('ddump') && function_exists('dump'))
{
    /**
     * Dump a variable and exit the script
     *
     * @param $var
     */
    function ddump($var)
    {
        dump($var);
        exit;
    }
}

if (!function_exists('process'))
{
    /**
     * Run a shell command with the Symfony Process class
     * Give a valid output parameter if you want realtime feedback
     *
     * @param                                                   $command
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Symfony\Component\Process\Process
     */
    function process($command, \Symfony\Component\Console\Output\OutputInterface $output = null)
    {
        $process = new \Symfony\Component\Process\Process($command);

        if (empty($output))
        {
            $process->run();
        }
        else
        {
            $process->run(function ($type, $buffer) use ($output)
            {
                if ('err' === $type)
                {
                    $output->writeln('<error>'.trim($buffer).'</error>');
                }
                else
                {
                    $output->writeln(trim($buffer));
                }
            });
        }

        return $process;
    }
}

if (!function_exists('processAtProjectRoot'))
{
    /**
     * Run a shell commmand at the project root (in the artisan file folder)
     *
     * @param                                                   $command
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return \Symfony\Component\Process\Process
     */
    function processAtProjectRoot($command, \Symfony\Component\Console\Output\OutputInterface $output = null)
    {
        $command = 'cd '.base_path().'; '.$command;

        return process($command, $output);
    }
}

if (!function_exists('workbench_path'))
{
    /**
     * Get an absolute path from a GroupEat package path relative to its src folder
     * Behave like the others Laravel *_path functions
     *
     * @param        $package The GroupEat package to use
     * @param string $file
     *
     * @return string
     */
    function workbench_path($package, $file = '')
    {
        $package = strtolower($package);
        $workbench_root = base_path('workbench/groupeat/'.$package.'/src');

        if (empty($file))
        {
            return $workbench_root;
        }
        else
        {
            return $workbench_root.'/'.$file;
        }
    }
}

if (!function_exists('listGroupeatPackages'))
{
    /**
     * Get the list of the GroupEat packages with the same case than the corresponding folders
     *
     * @param bool $withoutCore
     *
     * @return array
     */
    function listGroupeatPackages($withoutCore = false)
    {
        $directories = \Illuminate\Support\Facades\File::directories(base_path('workbench/groupeat'));

        $packages = array_map(function($directory)
        {
            $parts = explode('/', $directory);

            return array_pop($parts);
        }, $directories);

        if ($withoutCore)
        {
            return array_filter($packages, function($package) { return $package != 'core'; });
        }
        else
        {
            return $packages;
        }
    }

    /**
     * Same as above but without the Core package
     *
     * @return array
     */
    function listGroupeatPackagesWithoutCore()
    {
        return listGroupeatPackages(true);
    }
}
