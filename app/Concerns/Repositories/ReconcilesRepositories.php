<?php

declare(strict_types=1);

namespace App\Concerns\Repositories;

use App\Actions\ActionResult;
use App\Actions\Repositories\ReconcileRepositoriesAction;
use App\Contracts\Repositories\RepositoryInterface;
use App\Enums\Actions\ActionStatus;
use Exception;

/**
 * Trait ReconcilesRepositories.
 */
trait ReconcilesRepositories
{
    /**
     * Reconcile repositories.
     *
     * @param  array  $data
     * @return ActionResult
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

        $action = $this->action();

        return $action->reconcileRepositories($sourceRepository, $destinationRepository);
    }

    /**
     * Get the reconcile action.
     *
     * @return ReconcileRepositoriesAction
     */
    abstract protected function action(): ReconcileRepositoriesAction;

    /**
     * Get source repository.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    abstract protected function getSourceRepository(array $data = []): ?RepositoryInterface;

    /**
     * Get destination repository.
     *
     * @param  array  $data
     * @return RepositoryInterface|null
     */
    abstract protected function getDestinationRepository(array $data = []): ?RepositoryInterface;

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
        // Not supported by default
    }
}
