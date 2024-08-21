<?php

declare(strict_types=1);

namespace App\Filament\TableActions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\TableActions\BaseTableAction;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
     * Run this after the video is uploaded.
     *
     * @param  BaseModel  $model
     * @param  array  $data
     * @return void
     */
    protected function afterUploaded(BaseModel $model, array $fields): void
    {
    }

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

        $model = $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());
            return;
        }

        $this->afterUploaded($model, $fields);

        $livewire = $this->getLivewire();
        if ($livewire instanceof BaseRelationManager) {
            $relation = $livewire->getRelationship();
            $pivot = $model;

            if ($relation instanceof BelongsToMany) {
                $pivotClass = $relation->getPivotClass();

                $pivot = $pivotClass::query()
                    ->where($livewire->getOwnerRecord()->getKeyName(), $livewire->getOwnerRecord()->getKey())
                    ->where($model->getKeyName(), $model->getKey())
                    ->first();
            }

            $this->updateLog($model, $pivot);
        }
    }
}
