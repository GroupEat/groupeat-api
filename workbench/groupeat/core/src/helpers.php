<?php

if (!function_exists('artisan'))
{
    // Call an Artisan command and return its output
    function artisan($command, $parameters = [], $verbosity = null)
    {
        $output = new \Symfony\Component\Console\Output\BufferedOutput($verbosity);
        \Illuminate\Support\Facades\Artisan::call($command, $parameters, $output);

        return $output->fetch();
    }
}

if (!function_exists('process'))
{
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

if (!function_exists('workbench_path'))
{
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

    function listGroupeatPackagesWithoutCore()
    {
        return listGroupeatPackages(true);
    }
}
