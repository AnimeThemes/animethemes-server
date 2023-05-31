<?php

declare(strict_types=1);

namespace App\Nova\Actions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\ActionResponse;
use Laravel\Nova\Fields\ActionFields;

/**
 * Class StorageAction.
 */
abstract class StorageAction extends Action
{
    /**
     * Get the underlying storage action.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return BaseStorageAction
     */
    abstract protected function action(ActionFields $fields, Collection $models): BaseStorageAction;

    /**
     * Perform the action on the given models.
     *
     * @param  ActionFields  $fields
     * @param  Collection  $models
     * @return ActionResponse
     */
    public function handle(ActionFields $fields, Collection $models): ActionResponse
    {
        $action = $this->action($fields, $models);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();

        if ($actionResult->hasFailed()) {
            return Action::danger($actionResult->getMessage());
        }

        return Action::message($actionResult->getMessage());
    }
}
