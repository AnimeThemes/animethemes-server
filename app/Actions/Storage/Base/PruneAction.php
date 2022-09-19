<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Contracts\Actions\Storage\StorageAction;
use App\Contracts\Actions\Storage\StorageResults;
use App\Contracts\Storage\InteractsWithDisk;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

/**
 * Class PruneAction.
 */
abstract class PruneAction implements InteractsWithDisk, StorageAction
{
    use ReconcilesRepositories;

    /**
     * Create a new action instance.
     *
     * @param  int  $hours
     */
    public function __construct(protected readonly int $hours = 72)
    {
    }

    /**
     * Handle action.
     *
     * @return StorageResults
     */
    public function handle(): StorageResults
    {
        $fs = Storage::disk($this->disk());
        $pruneDate = Date::now()->subHours($this->hours);

        $results = [];

        foreach ($fs->allFiles() as $path) {
            $lastModified = Date::createFromTimestamp($fs->lastModified($path));
            if ($lastModified->isBefore($pruneDate)) {
                $result = $fs->delete($path);

                $results[$path] = $result;
            }
        }

        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();

        return new PruneResults($this->disk(), $results);
    }
}
