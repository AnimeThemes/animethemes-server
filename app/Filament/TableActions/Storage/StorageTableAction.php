<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use App\Filament\TableActions\BaseTableAction;

/**
 * Class StorageTableAction.
 */
abstract class StorageTableAction extends BaseTableAction
{
    /**
     * Get the underlying storage action.
     *
     * @param  array  $fields
     * @return BaseStorageAction
     */
    abstract protected function storageAction(array $fields): BaseStorageAction;

    /**
     * Perform the action on the table.
     *
     * @param  array  $fields
     * @return void
     */
    public function handle(array $fields): void
    {
        $action = $this->storageAction($fields);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();
    }
}
