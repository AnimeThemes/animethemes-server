<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

/**
 * Class StorageHeaderAction.
 */
abstract class StorageHeaderAction extends Action
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (Model $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  Model  $model
     * @param  array  $fields
     * @return BaseStorageAction
     */
    abstract protected function storageAction(Model $model, array $fields): BaseStorageAction;

    /**
     * Perform the action on the given models.
     *
     * @param  Model  $model
     * @param  array  $fields
     * @return void
     */
    public function handle(Model $model, array $fields): void
    {
        $action = $this->storageAction($model, $fields);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();
    }
}
