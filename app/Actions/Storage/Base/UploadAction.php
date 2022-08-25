<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\ReconcileResults;
use App\Actions\Storage\StorageAction;
use App\Actions\Storage\StorageResults;
use App\Contracts\Repositories\RepositoryInterface;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Class UploadAction.
 */
abstract class UploadAction extends StorageAction
{
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

        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();

        return new UploadResults($results);
    }

    /**
     * Reconcile storage repositories.
     *
     * @return ReconcileResults
     */
    protected function reconcileRepositories(): ReconcileResults
    {
        $action = $this->action();

        $sourceRepository = $this->getSourceRepository();
        $destinationRepository = $this->getDestinationRepository();

        $this->handleFilters($sourceRepository, $destinationRepository);

        return $action->reconcileRepositories($sourceRepository, $destinationRepository);
    }

    /**
     * Get source repository for action.
     *
     * @return RepositoryInterface
     */
    abstract protected function getSourceRepository(): RepositoryInterface;

    /**
     * Get destination repository for action.
     *
     * @return RepositoryInterface
     */
    abstract protected function getDestinationRepository(): RepositoryInterface;

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    protected function handleFilters(
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void {
        $sourceRepository->handleFilter('path', $this->path);
        $destinationRepository->handleFilter('path', $this->path);
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositories
     */
    abstract protected function action(): ReconcileRepositories;
}
