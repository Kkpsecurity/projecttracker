<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (config('backup.enable_cron') && cache('backup_cron_enabled', true)) {
            $schedule->command('backup:auto')
                ->daily()
                ->at(config('backup.cron_time_at', '23:00'))
                ->emailOutputOnFailure(config('backup.admin_email', 'richievc@gmail.com'));

            Log::info('[Cron Enabled] Scheduled backup:auto at '.config('backup.cron_time_at', '23:00'));
        } else {
            Log::info('[Cron Disabled] Skipping scheduled backup:auto');
        }
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
