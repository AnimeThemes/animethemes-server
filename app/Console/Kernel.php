<?php

namespace App\Console;

use App\Console\Commands\SyncVideosCommand;
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
        \App\Console\Commands\AnnouncementCreateCommand::class,
        \App\Console\Commands\AnnouncementReadCommand::class,
        \App\Console\Commands\AnnouncementUpdateCommand::class,
        \App\Console\Commands\AnnouncementDeleteCommand::class,
        \App\Console\Commands\AnnouncementListCommand::class,
        \App\Console\Commands\SyncVideosCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(SyncVideosCommand::class)->daily();
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
