<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Contracts\Actions\Storage\StorageAction;
use App\Contracts\Actions\Storage\StorageResults;
use App\Contracts\Storage\InteractsWithDisk;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;

abstract class PruneAction implements InteractsWithDisk, StorageAction
{
    use ReconcilesRepositories;

    public function __construct(protected readonly int $hours = 72) {}

    /**
/
    public function handle(): StorageResults
    {
        $fs = Storage::disk($this->disk());
        $pruneDate = Date::now()->subHours($this->hours);

        $results = [];

        foreach ($fs->allFiles() as $path) {
            $lastModified = Date::createFromTimestamp($fs->lastModified($path));
            if ($lastModified->isBefore($pruneDate) && $this->shouldBePruned($path, $lastModified)) {
                $result = $fs->delete($path);

                $results[$path] = $result;
            }
        }

        return new PruneResults($this->disk(), $results);
    }

    /**
     * Processes to be completed after handling action.
     *
     * @throws Exception
     */
    public function then(StorageResults $storageResults): null
    {
        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();

        return null;
    }

    /**
     * Determine whether the file should be pruned.
     */
    abstract protected function shouldBePruned(string $path, Carbon $lastModified): bool;
}
