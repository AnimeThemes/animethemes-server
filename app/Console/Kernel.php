<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\Models\SyncLikeAggregatesCommand;
use App\Console\Commands\Models\SyncViewAggregatesCommand;
use App\Console\Commands\Repositories\Storage\Admin\DumpReconcileCommand;
use App\Console\Commands\Storage\Admin\AdminDumpCommand;
use App\Console\Commands\Storage\Admin\AuthDumpCommand;
use App\Console\Commands\Storage\Admin\DiscordDumpCommand;
use App\Console\Commands\Storage\Admin\DocumentDumpCommand;
use App\Console\Commands\Storage\Admin\DumpPruneCommand;
use App\Console\Commands\Storage\Admin\ListDumpCommand;
use App\Console\Commands\Storage\Admin\UserDumpCommand;
use App\Console\Commands\Storage\Admin\WikiDumpCommand;
use App\Models\BaseModel;
use BezhanSalleh\FilamentExceptions\Models\Exception;
use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Cache\Console\PruneStaleTagsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\MonitorCommand as MonitorDatabaseCommand;
use Illuminate\Database\Console\PruneCommand as PruneModelsCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Queue\Console\MonitorCommand as MonitorQueueCommand;
use Illuminate\Queue\Console\PruneBatchesCommand;
use Illuminate\Queue\Console\PruneFailedJobsCommand;
use Laravel\Horizon\Console\SnapshotCommand;
use Laravel\Sanctum\Console\Commands\PruneExpired;
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
        $schedule->command(ClearResetsCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->everyFifteenMinutes();

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

        $schedule->command(AdminDumpCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->weeklyOn(Schedule::MONDAY);

        $schedule->command(AuthDumpCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->weeklyOn(Schedule::MONDAY);

        $schedule->command(DiscordDumpCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->weeklyOn(Schedule::MONDAY);

        $schedule->command(ListDumpCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->weeklyOn(Schedule::MONDAY);

        $schedule->command(UserDumpCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->weeklyOn(Schedule::MONDAY);

        $schedule->command(DumpReconcileCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->dailyAt('00:10');

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

        $schedule->command(PruneBatchesCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

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

        $schedule->command(PruneModelsCommand::class, ['--model' => [Exception::class]]) // Filament Exception Viewer Plugin
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        $schedule->command(PruneStaleTagsCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->hourly();

        $schedule->command(SnapshotCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->everyFiveMinutes();

        $schedule->command(SyncLikeAggregatesCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

        $schedule->command(SyncViewAggregatesCommand::class)
            ->withoutOverlapping()
            ->runInBackground()
            ->storeOutput()
            ->daily();

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
