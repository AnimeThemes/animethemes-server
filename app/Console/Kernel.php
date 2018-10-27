<?php

namespace App\Console;

use App\Console\Commands\SyncVideosCommand;
use App\Console\Commands\SyncRedditCommand;
use App\Console\Commands\SyncSeriesCommand;
use App\Console\Commands\SyncArtistommand;
use App\Console\Commands\SyncKitsuCommand;
use App\Console\Commands\SyncAnilistCommand;
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
        \App\Console\Commands\SyncVideosCommand::class,
        \App\Console\Commands\SyncRedditCommand::class,
        \App\Console\Commands\SyncSeriesCommand::class,
        \App\Console\Commands\SyncArtistCommand::class,
        \App\Console\Commands\SyncKitsuCommand::class,
        \App\Console\Commands\SyncAnilistCommand::class
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
        $schedule->command(SyncRedditCommand::class)->daily();
        $schedule->command(SyncSeriesCommand::class)->daily();
        $schedule->command(SyncArtistCommand::class)->daily();
        $schedule->command(SyncAnilistCommand::class)->daily();
        $schedule->command(SyncKitsuCommand::class)->daily();
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
