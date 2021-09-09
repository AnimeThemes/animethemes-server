<?php

declare(strict_types=1);

namespace App\Console\Commands\Wiki;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class PruneDatabaseDumpsCommand.
 */
class PruneDatabaseDumpsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:prune-dumps {--H|hours=72 : The number of hours to retain sanitized database dumps}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale database dumps from local storage';

    /**
     * The number of dumps deleted.
     *
     * @var int
     */
    protected int $deleted = 0;

    /**
     * The number of dumps whose deletion failed.
     *
     * @var int
     */
    protected int $deletedFailed = 0;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $hours = $this->option('hours');
        if (! is_numeric($hours)) {
            Log::error("Invalid hours value '{$hours}'");
            $this->error("Invalid hours value '{$hours}'");

            return 1;
        }

        $this->prune(
            Storage::disk('db-dumps'),
            Date::now()->subHours(intval($hours))
        );

        $this->printResults();

        return 0;
    }

    /**
     * Prune database dumps in filesystem against date.
     *
     * @param  Filesystem  $filesystem
     * @param  Carbon  $pruneDate
     * @return void
     */
    protected function prune(Filesystem $filesystem, Carbon $pruneDate)
    {
        foreach ($filesystem->allFiles() as $path) {
            $lastModified = Date::createFromTimestamp($filesystem->lastModified($path));
            if (Str::contains($path, 'animethemes-db-dump') && $lastModified->isBefore($pruneDate)) {
                $result = $filesystem->delete($path);
                if ($result) {
                    $this->deleted++;
                    Log::info("Deleted database dump '{$path}'");
                    $this->info("Deleted database dump '{$path}'");
                } else {
                    $this->deletedFailed++;
                    Log::error("Failed to delete database dump '{$path}'");
                    $this->error("Failed to delete database dump '{$path}'");
                }
            }
        }
    }

    /**
     * Print results to console and logs.
     *
     * @return void
     */
    protected function printResults()
    {
        if ($this->hasResults()) {
            if ($this->hasDeletions()) {
                Log::info("{$this->deleted} database dumps deleted");
                $this->info("{$this->deleted} database dumps deleted");
            }
            if ($this->hasFailures()) {
                Log::error("Failed to delete {$this->deletedFailed} database dumps");
                $this->error("Failed to delete {$this->deletedFailed} database dumps");
            }
        } else {
            Log::info('No database dumps deleted');
            $this->info('No database dumps deleted');
        }
    }

    /**
     * Determines if any deletions, successful or not, were made during pruning.
     *
     * @return bool
     */
    protected function hasResults(): bool
    {
        return $this->hasDeletions() || $this->hasFailures();
    }

    /**
     * Determines if any successful deletions were made during pruning.
     *
     * @return bool
     */
    protected function hasDeletions(): bool
    {
        return $this->deleted > 0;
    }

    /**
     * Determines if any unsuccessful deletions were attempted during pruning.
     *
     * @return bool
     */
    protected function hasFailures(): bool
    {
        return $this->deletedFailed > 0;
    }
}
