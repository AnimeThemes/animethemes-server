<?php

declare(strict_types=1);

namespace App\Filament\HeaderActions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use App\Filament\HeaderActions\BaseHeaderAction;
use App\Models\BaseModel;

/**
 * Class StorageHeaderAction.
 */
abstract class StorageHeaderAction extends BaseHeaderAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (BaseModel $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @return BaseStorageAction
     */
    abstract protected function storageAction(BaseModel $model, array $fields): BaseStorageAction;

    /**
     * Perform the action on the given models.
     *
     * @param  BaseModel  $model
     * @param  array  $fields
     * @return void
     */
    public function handle(BaseModel $model, array $fields): void
    {
        $action = $this->storageAction($model, $fields);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());
        }
    }
}
