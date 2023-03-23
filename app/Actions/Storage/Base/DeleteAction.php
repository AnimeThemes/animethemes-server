<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Contracts\Actions\Storage\StorageAction;
use App\Contracts\Actions\Storage\StorageResults;
use App\Contracts\Repositories\RepositoryInterface;
use App\Contracts\Storage\InteractsWithDisks;
use App\Models\BaseModel;
use Exception;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class DeleteAction.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class DeleteAction implements InteractsWithDisks, StorageAction
{
    use ReconcilesRepositories;

    /**
     * Create a new action instance.
     *
     * @param  TModel  $model
     */
    public function __construct(protected readonly BaseModel $model)
    {
    }

    /**
     * Handle action.
     *
     * @return StorageResults
     */
    public function handle(): StorageResults
    {
        $results = [];

        foreach ($this->disks() as $disk) {
            /** @var FilesystemAdapter $fs */
            $fs = Storage::disk($disk);

            $result = $fs->delete($this->path());

            $results[$disk] = $result;
        }

        return new DeleteResults($this->model, $results);
    }

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @param  array  $data
     * @return void
     */
    protected function handleFilters(
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository,
        array $data = []
    ): void {
        $sourceRepository->handleFilter('path', File::dirname($this->path()));
        $destinationRepository->handleFilter('path', File::dirname($this->path()));
    }

    /**
     * Processes to be completed after handling action.
     *
     * @param  StorageResults  $storageResults
     * @return void
     *
     * @throws Exception
     */
    public function then(StorageResults $storageResults): void
    {
        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();
    }

    /**
     * Get the path to delete.
     *
     * @return string
     */
    abstract protected function path(): string;
}
