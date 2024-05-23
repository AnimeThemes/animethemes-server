<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use Filament\Tables\Actions\Action;

/**
 * Class StorageTableAction.
 */
abstract class StorageTableAction extends Action
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (array $data) => $this->handle($data));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array  $fields
     * @return BaseStorageAction
     */
    abstract protected function storageAction(array $fields): BaseStorageAction;

    /**
     * Perform the action on the given models.
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
