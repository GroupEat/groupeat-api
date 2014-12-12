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

if (!function_exists('workbench_path'))
{
    // Call an Artisan command and return its output
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
    // Call an Artisan command and return its output
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
