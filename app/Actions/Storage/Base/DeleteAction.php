<?php

declare(strict_types=1);

namespace App\Actions\Storage\Base;

use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\ReconcileResults;
use App\Actions\Storage\StorageAction;
use App\Actions\Storage\StorageResults;
use App\Contracts\Repositories\RepositoryInterface;
use App\Models\BaseModel;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Class DeleteAction.
 *
 * @template TModel of \App\Models\BaseModel
 */
abstract class DeleteAction extends StorageAction
{
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

        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();

        return new DeleteResults($this->model, $results);
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
        $sourceRepository->handleFilter('path', File::dirname($this->path()));
        $destinationRepository->handleFilter('path', File::dirname($this->path()));
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositories
     */
    abstract protected function action(): ReconcileRepositories;

    /**
     * Get the path to delete.
     *
     * @return string
     */
    abstract protected function path(): string;
}
