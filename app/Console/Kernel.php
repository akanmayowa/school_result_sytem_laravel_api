<?php

namespace App\Console;

use App\Jobs\CalculateResult;
use App\Jobs\CalculateResultOne;
use App\Jobs\CalculateResultIncourse;
use App\Jobs\CalculateResultTwo;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new CalculateResultOne)->everyMinute();
        $schedule->job(new CalculateResultTwo)->everyMinute();
        $schedule->job(new CalculateResultIncourse)->everyMinute();
        // $schedule->command('inspire')->hourly();
        $schedule->command('exam_offender_log:cron')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
