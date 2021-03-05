<?php

namespace App\Console;

use App\Console\Cron\Hourly;
use App\Console\Cron\Monthly;
use App\Console\Cron\Nightly;
use App\Console\Cron\Weekly;
use App\Services\CronService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule. How this works... according to the command
     * time, an event gets send out with the appropriate time (e.g, hourly sends an hourly event)
     *
     * Then the CronServiceProvider has the list of cronjobs which then run according to the events
     * and then calls those at the proper times.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(Nightly::class)->dailyAt('01:00');
        $schedule->command(Weekly::class)->weeklyOn(0);
        $schedule->command(Monthly::class)->monthlyOn(1);
        $schedule->command(Hourly::class)->hourly();

        // When spatie-backups runs
        /*if (config('backup.backup.enabled', false) === true) {
            $schedule->command('backup:clean')->daily()->at('01:00');
            $schedule->command('backup:run')->daily()->at('02:00');
        }*/

        // Update the last time the cron was run
        /** @var CronService $cronSvc */
        $cronSvc = app(CronService::class);
        $cronSvc->updateLastRunTime();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        $this->load(__DIR__.'/Cron');
    }
}
