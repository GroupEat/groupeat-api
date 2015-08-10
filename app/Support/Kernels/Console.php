<?php
namespace Groupeat\Support\Kernels;

use Groupeat\Support\Values\Environment;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;

class Console extends Kernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule)
    {
        $env = app(Environment::class);

        $schedule->command('group-orders:close')->cron('* * * * *')->withoutOverlapping();
        $schedule->command('group-orders:detect-uncomfirmed')->cron('* * * * *')->withoutOverlapping();

        if ($env->isStaging() || $env->isProduction()) {
            $schedule->command('db:backup --s3')->dailyAt('04:00');
        }
    }
}
