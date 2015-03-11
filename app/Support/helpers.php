<?php

if (!function_exists('artisan')) {
    /**
     * Call an Artisan command and return its output.
     *
     * @param string $command    Command name (like groupeat:push)
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

if (!function_exists('formatTime')) {
    /**
     * @param \Carbon\Carbon $time
     * @param string         $hoursSuffix
     * @param bool           $withSeconds
     *
     * @return string
     */
    function formatTime(\Carbon\Carbon $time, $hoursSuffix = '\h', $withSeconds = false)
    {
        $format = 'H'.$hoursSuffix.'i';

        if ($withSeconds) {
            $format .= ':s';
        }

        return $time->format($format);
    }
}

if (!function_exists('dbTransaction')) {
    /**
     * Execute callback in a database transaction.
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

if (!function_exists('assertSameDay')) {
    function assertSameDay(\Carbon\Carbon $one, \Carbon\Carbon $two)
    {
        if ($one->toDateString() != $two->toDateString()) {
            throw new \Groupeat\Support\Exceptions\BadRequest(
                'dateTimesMustBeFromSameDay',
                "The DateTime $one must be from the same day thant $two."
            );
        }
    }
}

if (!function_exists('getNamespaceOf')) {
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

if (!function_exists('getClassNameWithoutNamespace')) {
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

if (!function_exists('removeNamespaceFromClassName')) {
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

if (!function_exists('translateIfNeeded')) {
    /**
     * If the text contains corresponds to a lang key its translation will be returned
     * Else the untounched text is returned.
     *
     * @param string $text
     *
     * @return string
     */
    function translateIfNeeded($text)
    {
        if (preg_match('/^\w+((\.|::)\w+)+\w+$/', $text)) {
            return trans($text);
        }

        return $text;
    }
}

if (!function_exists('mb_ucfirst')) {
    /**
     * Return the given string with the first letter uppercased.
     *
     * @param string  $str      The string to use
     * @param boolean $lowerEnd Indicates if the rest of the string should be lowercased
     * @param string  $encoding Encoding type
     *
     * @return string
     */
    function mb_ucfirst($str, $lowerEnd = false, $encoding = 'UTF-8')
    {
        $firstLetter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);

        if ($lowerEnd) {
            return $firstLetter.mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }

        return $firstLetter.mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
}

if (!function_exists('mb_lcfirst')) {
    /**
     * Return the given string with the first letter lowercased.
     *
     * @param string  $str      The string to use
     * @param boolean $upperEnd Indicates if the rest of the string should be uppercased
     * @param string  $encoding Encoding type
     *
     * @return string
     */
    function mb_lcfirst($str, $upperEnd = false, $encoding = 'UTF-8')
    {
        $firstLetter = mb_strtolower(mb_substr($str, 0, 1, $encoding), $encoding);

        if ($upperEnd) {
            return $firstLetter.mb_strtoupper(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }

        return $firstLetter.mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice(\SebastianBergmann\Money\Money $price)
    {
        // TODO: Add support for other locales
        return (new \SebastianBergmann\Money\IntlFormatter('fr_FR'))->format($price);
    }
}

if (!function_exists('sumPrices')) {
    /**
     * @param \Illuminate\Support\Collection $prices
     *
     * @return \SebastianBergmann\Money\Money
     */
    function sumPrices(\Illuminate\Support\Collection $prices)
    {
        if ($prices->isEmpty()) {
            return new \SebastianBergmann\Money\EUR(0); // TODO: Find out how to choose default currency
        }

        $total = new \SebastianBergmann\Money\Money(0, $prices->first()->getCurrency());

        foreach ($prices as $price) {
            $total = $total->add($price);
        }

        return $total;
    }
}

if (!function_exists('process')) {
    /**
     * Run a shell command with the Symfony Process class.
     * Give a valid output parameter if you want realtime feedback.
     *
     * @param string                                            $command
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param string                                            $workingDirectory Null for project root
     * @param int                                               $timeoutInSeconds Null for no timeout
     *
     * @return \Symfony\Component\Process\Process
     */
    function process(
        $command,
        \Symfony\Component\Console\Output\OutputInterface $output = null,
        $workingDirectory = null,
        $timeoutInSeconds = 60
    ) {
        if (is_null($workingDirectory)) {
            $workingDirectory = base_path();
        }

        $process = new \Symfony\Component\Process\Process(
            $command,
            $workingDirectory,
            null,
            null,
            $timeoutInSeconds
        );

        if (empty($output)) {
            $process->run();
        } else {
            $process->run(function ($type, $buffer) use ($output) {
                if ('err' === $type) {
                    $output->writeln('<error>'.trim($buffer).'</error>');
                } else {
                    $output->writeln(trim($buffer));
                }
            });
        }

        return $process;
    }
}

if (!function_exists('listGroupeatPackages')) {
    /**
     * Get the list of the GroupEat packages with the same case than the corresponding folders.
     *
     * @param bool $withoutSupport
     *
     * @return array
     */
    function listGroupeatPackages($withoutSupport = false)
    {
        $directories = \Illuminate\Support\Facades\File::directories(app_path());

        $packages = array_map(function ($directory) {
            $parts = explode('/', $directory);

            return array_pop($parts);
        }, $directories);

        if ($withoutSupport) {
            return array_filter($packages, function ($package) {
                return $package != 'Support';
            });
        } else {
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