<?php

if (!function_exists('artisan'))
{
    /**
     * Call an Artisan command and return its output.
     *
     * @param string $command Command name (like groupeat:push)
     * @param array  $parameters Command options
     * @param int    $verbosity
     *
     * @return string Command output
     */
    function artisan($command, $parameters = [], $verbosity = null)
    {
        $output = new \Symfony\Component\Console\Output\BufferedOutput($verbosity);
        Artisan::call($command, $parameters, $output);

        return $output->fetch();
    }
}

if (!function_exists('ddd') && function_exists('dump'))
{
    /**
     * Dump a variable and exit the script.
     *
     * @param $var
     */
    function ddd($var)
    {
        dump($var);
        exit;
    }
}

if (!function_exists('dbTransaction'))
{
    /**
     * Execute callback in a database transaction
     *
     * @param Closure $callback
     *
     * @return mixed
     */
    function dbTransaction(Closure $callback)
    {
        return DB::transaction($callback);
    }
}

if (!function_exists('decodeJSON'))
{
    /**
     * Decode JSON
     *
     * @param string $JSON
     *
     * @return array
     */
    function decodeJSON($JSON, $throwOnNull = true)
    {
        $data = json_decode($JSON, true);

        if (is_null($data) && $throwOnNull)
        {
            throw new \Groupeat\Support\Exceptions\BadRequest("Cannot decode JSON: $JSON.");
        }

        return $data;
    }
}

if (!function_exists('getNamespaceOf'))
{
    /**
     * Get the namespace of the given class.
     *
     * @param $class
     *
     * @return string
     */
    function getNamespaceOf($class)
    {
        $className = get_class($class);

        return substr($className, 0, strrpos($className, '\\'));
    }
}

if (!function_exists('getClassNameWithoutNamespace'))
{
    /**
     * Get the name of a class without its namespace.
     *
     * @param $class
     *
     * @return string
     */
    function getClassNameWithoutNamespace($class)
    {
        return removeNamespaceFromClassName(get_class($class));
    }
}

if (!function_exists('removeNamespaceFromClassName'))
{
    /**
     * Remove the namespace from a class name.
     *
     * @param string $className
     *
     * @return string
     */
    function removeNamespaceFromClassName($className)
    {
        $parts = explode('\\', $className);

        return array_pop($parts);
    }
}

if (!function_exists('translateIfNeeded'))
{
    /**
     * If the text contains corresponds to a lang key its translation will be returned
     * Else the untounched text is returned
     *
     * @param string $text
     *
     * @return string
     */
    function translateIfNeeded($text)
    {
        if (preg_match('/^\w+((\.|::)\w+)+\w+$/', $text))
        {
            return \Lang::get($text);
        }

        return $text;
    }
}

if (!function_exists('panelView'))
{
    /**
     * @param string $title
     * @param mixed  $panelBody
     * @param string $panelClass
     * @param string $panelId
     *
     * @return \Illuminate\View\View
     */
    function panelView($title, $panelBody, $panelClass = 'primary', $panelId = 'groupeat-panel')
    {
        $title = translateIfNeeded($title);

        if ($panelBody instanceof \Groupeat\Support\Forms\Form)
        {
            $panelBody = $panelBody->render();
        }

        return \View::make('support::panel', compact('title', 'panelBody', 'panelClass', 'panelId'));
    }
}

if (!function_exists('process'))
{
    /**
     * Run a shell command with the Symfony Process class.
     * Give a valid output parameter if you want realtime feedback.
     *
     * @param string                                            $command
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string or null for project root                   $workingDirectory
     * @param int or null for no-timeout                        $timeoutInSeconds
     *
     * @return \Symfony\Component\Process\Process
     */
    function process(
        $command,
        \Symfony\Component\Console\Output\OutputInterface $output = null,
        $workingDirectory = null,
        $timeoutInSeconds = 60
    )
    {
        if (is_null($workingDirectory))
        {
            $workingDirectory = base_path();
        }

        $process = new \Symfony\Component\Process\Process(
            $command,
            $workingDirectory,
            null,
            null,
            $timeoutInSeconds
        );

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
    /**
     * Get an absolute path from a GroupEat package path relative to its src folder.
     * Behave like the others Laravel *_path functions.
     *
     * @param string $package GroupEat package to use
     * @param string $file
     *
     * @return string
     */
    function workbench_path($package, $file = '')
    {
        $package = strtolower($package);
        $workbench_root = base_path("workbench/groupeat/$package/src");

        if (empty($file))
        {
            return $workbench_root;
        }
        else
        {
            return "$workbench_root/$file";
        }
    }
}

if (!function_exists('listGroupeatPackages'))
{
    /**
     * Get the list of the GroupEat packages with the same case than the corresponding folders.
     *
     * @param bool $withoutSupport
     *
     * @return array
     */
    function listGroupeatPackages($withoutSupport = false)
    {
        $directories = \Illuminate\Support\Facades\File::directories(base_path('workbench/groupeat'));

        $packages = array_map(function($directory)
        {
            $parts = explode('/', $directory);

            return array_pop($parts);
        }, $directories);

        if ($withoutSupport)
        {
            return array_filter($packages, function($package) { return $package != 'Support'; });
        }
        else
        {
            return $packages;
        }
    }

    /**
     * Same as above but without the Support package.
     *
     * @return array
     */
    function listGroupeatPackagesWithoutSupport()
    {
        return listGroupeatPackages(true);
    }
}
