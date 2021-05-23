<?php

namespace App\Console;

use App\Console\Commands\DatabaseDumpCommand;
use App\Console\Commands\TransactionReconcileCommand;
use App\Console\Commands\VideoReconcileCommand;
use App\Enums\Billing\Service;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Telescope\Console\PruneCommand;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\DatabaseDumpCommand::class,
        \App\Console\Commands\TransactionReconcileCommand::class,
        \App\Console\Commands\VideoReconcileCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(DatabaseDumpCommand::class)->daily();
        $schedule->command(PruneCommand::class)->daily();
        $schedule->command(SnapshotCommand::class)->everyFiveMinutes();
        $schedule->command(TransactionReconcileCommand::class, ['service' => Service::DIGITALOCEAN()->key])->daily();
        $schedule->command(VideoReconcileCommand::class)->hourly();
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
