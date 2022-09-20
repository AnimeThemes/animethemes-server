<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Concerns\Repositories\ReconcilesRepositories;
use App\Contracts\Actions\Storage\StorageAction;
use App\Contracts\Actions\Storage\StorageResults;
use App\Contracts\Repositories\RepositoryInterface;
use App\Contracts\Storage\InteractsWithDisks;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Class UploadAction.
 */
abstract class UploadAction implements InteractsWithDisks, StorageAction
{
    use ReconcilesRepositories;

    /**
     * Create a new action instance.
     *
     * @param  UploadedFile  $file
     * @param  string  $path
     */
    public function __construct(protected readonly UploadedFile $file, protected readonly string $path)
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

            $result = $fs->putFileAs($this->path, $this->file, $this->file->getClientOriginalName());

            $results[$disk] = $result;
        }

        return new UploadResults($results);
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
        $sourceRepository->handleFilter('path', $this->path);
        $destinationRepository->handleFilter('path', $this->path);
    }

    /**
     * Processes to be completed after handling action.
     *
     * @param  StorageResults  $storageResults
     * @return void
     */
    public function then(StorageResults $storageResults): void
    {
        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();
    }
}
