<?php

declare(strict_types=1);

namespace App\Nova\Actions\Repositories;

use App\Concerns\Repositories\ReconcilesRepositories;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class ReconcileAction.
 */
abstract class ReconcileAction extends Action
{
    use ReconcilesRepositories;

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
        $result = $this->reconcileRepositories($fields->toArray());

        $result->toLog();

        if ($result->hasFailed()) {
            return Action::danger($result->getMessage());
        }

        return Action::message($result->getMessage());
    }
}
