<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\Billing\BalanceReconcileCommand;
use App\Console\Commands\Billing\TransactionReconcileCommand;
use App\Console\Commands\Document\DocumentDatabaseDumpCommand;
use App\Console\Commands\PruneDatabaseDumpsCommand;
use App\Console\Commands\Wiki\WikiDatabaseDumpCommand;
use App\Enums\Models\Billing\Service;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Queue\Console\PruneFailedJobsCommand;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Sanctum\Console\Commands\PruneExpired;
use Laravel\Telescope\Console\PruneCommand;

/**
 * Class Kernel.
 */
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(BalanceReconcileCommand::class, [Service::DIGITALOCEAN()->key])->hourly();
        $schedule->command(DocumentDatabaseDumpCommand::class)->daily();
        $schedule->command(WikiDatabaseDumpCommand::class)->daily();
        $schedule->command(WikiDatabaseDumpCommand::class, ['--create'])->daily();
        $schedule->command(PruneCommand::class)->daily();
        $schedule->command(PruneDatabaseDumpsCommand::class)->dailyAt('00:15');
        $schedule->command(PruneExpired::class)->daily();
        $schedule->command(PruneFailedJobsCommand::class)->daily();
        $schedule->command(SnapshotCommand::class)->everyFiveMinutes();
        $schedule->command(TransactionReconcileCommand::class, [Service::DIGITALOCEAN()->key])->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
