<?php

declare(strict_types=1);

namespace App\Concerns\Repositories;

use App\Actions\ActionResult;
use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Actions\ActionStatus;
use Exception;

trait ReconcilesRepositories
{
    /**
     * Reconcile repositories.
     *
     * @param  array  $data
     *
     * @throws Exception
     */
    protected function reconcileRepositories(array $data = []): ActionResult
    {
        $sourceRepository = $this->getSourceRepository($data);
        if ($sourceRepository === null) {
            return new ActionResult(
                ActionStatus::FAILED,
                'Could not find source repository'
            );
        }

        $destinationRepository = $this->getDestinationRepository($data);
        if ($destinationRepository === null) {
            return new ActionResult(
                ActionStatus::FAILED,
                'Could not find destination repository'
            );
        }

        $this->handleFilters($sourceRepository, $destinationRepository, $data);

        $action = $this->reconcileAction();

        return $action->reconcileRepositories($sourceRepository, $destinationRepository);
    }

    /**
     * Get the reconcile action.
     */
    abstract protected function reconcileAction(): ReconcileRepositoriesAction;

    /**
     * Get source repository.
     *
     * @param  array  $data
     */
    abstract protected function getSourceRepository(array $data = []): ?RepositoryInterface;

    /**
     * Get destination repository.
     *
     * @param  array  $data
     */
    abstract protected function getDestinationRepository(array $data = []): ?RepositoryInterface;

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  array  $data
     */
    protected function handleFilters(
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository,
        array $data = []
    ): void {
        // Not supported by default
    }
}
