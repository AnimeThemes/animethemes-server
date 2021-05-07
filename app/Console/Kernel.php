<?php

namespace App\Console;

use App\Console\Commands\DatabaseDumpCommand;
use App\Console\Commands\InvoiceReconcileCommand;
use App\Console\Commands\VideoReconcileCommand;
use App\Enums\InvoiceVendor;
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
        \App\Console\Commands\InvoiceReconcileCommand::class,
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
        $schedule->command(VideoReconcileCommand::class)->hourly();
        $schedule->command(InvoiceReconcileCommand::class, ['vendor' => InvoiceVendor::DIGITALOCEAN()->key])->monthlyOn(2);
        $schedule->command(SnapshotCommand::class)->everyFiveMinutes();
        $schedule->command(PruneCommand::class)->daily();
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
