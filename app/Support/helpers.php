<?php

if (!function_exists('artisan')) {
    // Call an Artisan command inside a DB transaction and return its output.
    function artisan(string $command, array $parameters = []): string
    {
        return DB::transaction(function () use ($command, $parameters) {
            Artisan::call($command, $parameters);
            return trim(Artisan::output());
        });
    }
}

if (!function_exists('formatTime')) {
    function formatTime(\Carbon\Carbon $time, string $hoursSuffix = '\h', bool $withSeconds = false): string
    {
        $format = 'H'.$hoursSuffix.'i';

        if ($withSeconds) {
            $format .= ':s';
        }

        return $time->format($format);
    }
}

if (!function_exists('getNamespaceOf')) {
    function getNamespaceOf($class): string
    {
        $className = get_class($class);

        return substr($className, 0, strrpos($className, '\\'));
    }
}

if (!function_exists('translateIfNeeded')) {
    // If the text corresponds to a lang key its translation will be returned, else the untounched text is returned.
    function translateIfNeeded(string $text): string
    {
        if (preg_match('/^\w+((\.|::)\w+)+\w+$/', $text)) {
            return trans($text);
        }

        return $text;
    }
}

if (!function_exists('mb_ucfirst')) {
    // Return the given string with the first letter uppercased.
    function mb_ucfirst(string $str, bool $lowerEnd = false, string $encoding = 'UTF-8'): string
    {
        $firstLetter = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding);

        if ($lowerEnd) {
            return $firstLetter.mb_strtolower(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }

        return $firstLetter.mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
}

if (!function_exists('mb_lcfirst')) {
    // Return the given string with the first letter lowercased.
    function mb_lcfirst(string $str, bool $upperEnd = false, string $encoding = 'UTF-8'): string
    {
        $firstLetter = mb_strtolower(mb_substr($str, 0, 1, $encoding), $encoding);

        if ($upperEnd) {
            return $firstLetter.mb_strtoupper(mb_substr($str, 1, mb_strlen($str, $encoding), $encoding), $encoding);
        }

        return $firstLetter.mb_substr($str, 1, mb_strlen($str, $encoding), $encoding);
    }
}

if (!function_exists('formatPrice')) {
    function formatPrice(\SebastianBergmann\Money\Money $price): string
    {
        $auth = app(\Groupeat\Auth\Auth::class);
        $userLocale = $auth->check() ? $auth->credentials()->locale : 'fr';
        $fullLocale = $userLocale.'_'.strtoupper($userLocale);

        return (new \SebastianBergmann\Money\IntlFormatter($fullLocale))->format($price);
    }
}

if (!function_exists('sumPrices')) {
    function sumPrices(\Illuminate\Support\Collection $prices): \SebastianBergmann\Money\Money
    {
        if ($prices->isEmpty()) {
            return new \SebastianBergmann\Money\EUR(0);
        }

        $total = new \SebastianBergmann\Money\Money(0, $prices->first()->getCurrency());

        foreach ($prices as $price) {
            $total = $total->add($price);
        }

        return $total;
    }
}

if (!function_exists('getPointFromLocationArray')) {
    function getPointFromLocationArray(array $location): \Phaza\LaravelPostgis\Geometries\Point
    {
        return new \Phaza\LaravelPostgis\Geometries\Point($location['latitude'], $location['longitude']);
    }
}

if (!function_exists('process')) {
    // Run a shell command with the Symfony Process class.
    // Give a valid output parameter if you want realtime feedback.
    // An empty $workingDirectory will execute the command in the current directory.
    // A $timeoutInSeconds of 0 corresponds to no timeout at all.
    function process(
        string $command,
        \Symfony\Component\Console\Output\OutputInterface $output = null,
        string $workingDirectory = '',
        $timeoutInSeconds = 60
    ): \Symfony\Component\Process\Process {
        if (!$workingDirectory) {
            $workingDirectory = base_path();
        }

        $process = new \Symfony\Component\Process\Process(
            $command,
            $workingDirectory,
            null,
            null,
            $timeoutInSeconds > 0 ? $timeoutInSeconds : null
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

if (!function_exists('getGroupeatPackagesCollection')) {
    // Get the collection of the GroupEat packages with the same case than the corresponding folders.
    function getGroupeatPackagesCollection(): \Illuminate\Support\Collection
    {
        return collect(glob(app_path('*'), GLOB_ONLYDIR))->map(function ($directory) {
            $segments = explode('/', $directory);

            return end($segments);
        });
    }
}
