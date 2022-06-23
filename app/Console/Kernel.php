<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\Billing\Balance\BalanceReconcileCommand;
use App\Console\Commands\Billing\Transaction\TransactionReconcileCommand;
use App\Console\Commands\Document\DocumentDatabaseDumpCommand;
use App\Console\Commands\PruneDatabaseDumpsCommand;
use App\Console\Commands\Wiki\WikiDatabaseDumpCommand;
use App\Enums\Models\Billing\Service;
use App\Models\BaseModel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand as PruneModelsCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Queue\Console\PruneFailedJobsCommand;
use Illuminate\Support\Facades\Config;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Sanctum\Console\Commands\PruneExpired;
use Laravel\Telescope\Console\PruneCommand as PruneTelescopeEntriesCommand;

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
        $schedule->command(BalanceReconcileCommand::class, [Service::DIGITALOCEAN()->key])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->hourly();

        // Managed database requires --single-transaction and --set-gtid-purged=OFF
        $schedule->command(DocumentDatabaseDumpCommand::class, ['--single-transaction', '--set-gtid-purged' => 'OFF'])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        // Managed database requires --single-transaction and --set-gtid-purged=OFF
        $schedule->command(WikiDatabaseDumpCommand::class, ['--single-transaction', '--set-gtid-purged' => 'OFF'])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        // Managed database requires --single-transaction and --set-gtid-purged=OFF
        $schedule->command(WikiDatabaseDumpCommand::class, ['--single-transaction', '--set-gtid-purged' => 'OFF', '--no-create-info'])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        $schedule->command(PruneDatabaseDumpsCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->dailyAt('00:15');

        $schedule->command(PruneExpired::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        $schedule->command(PruneFailedJobsCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        $schedule->command(PruneModelsCommand::class, ['--except' => [BaseModel::class]])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        if (Config::get('telescope.enabled', false)) {
            $schedule->command(PruneTelescopeEntriesCommand::class)
                ->withoutOverlapping()
                ->runInBackground()
                ->storeOutput()
                ->daily();
        }

        $schedule->command(SnapshotCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->everyFiveMinutes();

        $schedule->command(TransactionReconcileCommand::class, [Service::DIGITALOCEAN()->key])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->hourly();
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
