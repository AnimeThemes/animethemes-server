<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\Billing\BalanceReconcileCommand;
use App\Console\Commands\Billing\TransactionReconcileCommand;
use App\Console\Commands\DatabaseDumpCommand;
use App\Console\Commands\VideoReconcileCommand;
use App\Enums\Billing\Service;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Telescope\Console\PruneCommand;

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        BalanceReconcileCommand::class,
        TransactionReconcileCommand::class,
        DatabaseDumpCommand::class,
        VideoReconcileCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(BalanceReconcileCommand::class, [Service::DIGITALOCEAN()->key])->daily();
        $schedule->command(DatabaseDumpCommand::class)->daily();
        $schedule->command(PruneCommand::class)->daily();
        $schedule->command(SnapshotCommand::class)->everyFiveMinutes();
        $schedule->command(TransactionReconcileCommand::class, [Service::DIGITALOCEAN()->key])->daily();
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
