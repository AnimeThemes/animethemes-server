<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\Billing\BalanceReconcileCommand;
use App\Console\Commands\Billing\TransactionReconcileCommand;
use App\Console\Commands\Wiki\DatabaseDumpCommand;
use App\Console\Commands\Wiki\VideoReconcileCommand;
use App\Enums\Models\Billing\Service;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Queue\Console\PruneFailedJobsCommand;
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
        DatabaseDumpCommand::class,
        TransactionReconcileCommand::class,
        VideoReconcileCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command(BalanceReconcileCommand::class, [Service::DIGITALOCEAN()->key])->dailyAt('07:00');
        $schedule->command(DatabaseDumpCommand::class)->daily();
        $schedule->command(DatabaseDumpCommand::class, ['--create'])->daily();
        $schedule->command(PruneCommand::class)->daily();
        $schedule->command(PruneFailedJobsCommand::class)->daily();
        $schedule->command(SnapshotCommand::class)->everyFiveMinutes();
        $schedule->command(TransactionReconcileCommand::class, [Service::DIGITALOCEAN()->key])->dailyAt('07:00');
        $schedule->command(VideoReconcileCommand::class)->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
