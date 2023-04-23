<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\Repositories\Billing\Balance\BalanceReconcileCommand;
use App\Console\Commands\Repositories\Billing\Transaction\TransactionReconcileCommand;
use App\Console\Commands\Storage\Admin\DocumentDumpCommand;
use App\Console\Commands\Storage\Admin\DumpPruneCommand;
use App\Console\Commands\Storage\Admin\WikiDumpCommand;
use App\Enums\Models\Billing\Service;
use App\Models\BaseModel;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\MonitorCommand as MonitorDatabaseCommand;
use Illuminate\Database\Console\PruneCommand as PruneModelsCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Queue\Console\MonitorCommand as MonitorQueueCommand;
use Illuminate\Queue\Console\PruneFailedJobsCommand;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Sanctum\Console\Commands\PruneExpired;
use Laravel\Telescope\Console\PruneCommand as PruneTelescopeEntriesCommand;
use Propaganistas\LaravelDisposableEmail\Console\UpdateDisposableDomainsCommand;

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

        $schedule->command(DocumentDumpCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        $schedule->command(WikiDumpCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        $schedule->command(DumpPruneCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->dailyAt('00:15');

        $schedule->command(MonitorDatabaseCommand::class, ['--max' => 100])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->everyMinute();

        $schedule->command(MonitorQueueCommand::class, ['queues' => 'default', '--max' => 100])
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->everyMinute();

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

        $schedule->command(PruneStaleTagsCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->hourly();

        if (config('telescope.enabled') === true) {
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

        $schedule->command(UpdateDisposableDomainsCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->weekly();
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
