<?php

namespace App\Console;

use App\Console\Commands\CleanTempFolderCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (app()->environment('production')) {
            $schedule->command('backup:run --only-db --disable-notifications')->everyThirtyMinutes();
            $schedule->command('backup:run')->daily()->at('03:10');
            $schedule->command('backup:clean')->daily()->at('03:30');
        }

        $schedule->command(CleanTempFolderCommand::class)->dailyAt('3:00');
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
