<?php

declare(strict_types=1);

namespace App\Nova\Actions;

use App\Actions\Repositories\ReconcileRepositories;
use App\Contracts\Repositories\RepositoryInterface;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class ReconcileAction.
 */
abstract class ReconcileAction extends Action
{
    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return array
     *
     * @noinspection PhpUnusedParameterInspection
     */
    public function handle(ActionFields $fields, Collection $models): array
    {
        $sourceRepository = $this->getSourceRepository($fields);

        $destinationRepository = $this->getDestinationRepository($fields);

        $this->handleFilters($fields, $sourceRepository, $destinationRepository);

        $action = $this->getAction();

        $results = $action->reconcileRepositories($sourceRepository, $destinationRepository);

        $results->toLog();

        return Action::message($results->summary());
    }

    /**
     * Get source repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    abstract protected function getSourceRepository(ActionFields $fields): ?RepositoryInterface;

    /**
     * Get destination repository for action.
     *
     * @param  ActionFields  $fields
     * @return RepositoryInterface|null
     */
    abstract protected function getDestinationRepository(ActionFields $fields): ?RepositoryInterface;

    /**
     * Apply filters to repositories before reconciliation.
     *
     * @param  ActionFields  $fields
     * @param  RepositoryInterface  $sourceRepository
     * @param  RepositoryInterface  $destinationRepository
     * @return void
     */
    abstract protected function handleFilters(
        ActionFields $fields,
        RepositoryInterface $sourceRepository,
        RepositoryInterface $destinationRepository
    ): void;

    /**
     * Get the reconciliation action.
     *
     * @return ReconcileRepositories
     */
    abstract protected function getAction(): ReconcileRepositories;
}
