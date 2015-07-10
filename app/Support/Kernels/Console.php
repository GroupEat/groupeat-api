<?php
namespace Groupeat\Support\Kernels;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;

class Console extends Kernel
{
    protected $commands = [];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('group-orders:close')->cron('* * * * *')->withoutOverlapping();

        if (!app()->isLocal()) {
            $schedule->command('db:backup', ['--s3' => true])->dailyAt('04:00');
        }
    }
}
