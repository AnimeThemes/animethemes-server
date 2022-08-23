<?php

declare(strict_types=1);

namespace App\Actions\Storage;

use App\Actions\ActionResult;
use App\Actions\Repositories\ReconcileRepositories;
use App\Actions\Repositories\ReconcileResults;
use App\Contracts\Repositories\RepositoryInterface;

/**
 * Class StorageAction.
 */
abstract class StorageAction
{
    /**
     * Handle action.
     *
     * @return ActionResult
     */
    public function handle(): ActionResult
    {
        $storageResults = $this->handleStorageAction();

        $storageResults->toLog();

        $reconcileResults = $this->reconcileRepositories();

        $reconcileResults->toLog();

        return $storageResults->toActionResult();
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
     * Perform the storage action.
     *
     * @return StorageResults
     */
    abstract protected function handleStorageAction(): StorageResults;

    /**
     * Get the disks to update.
     *
     * @return array
     */
    abstract protected function disks(): array;

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
    abstract protected function handleFilters(
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void;

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositories
     */
    abstract protected function action(): ReconcileRepositories;
}
