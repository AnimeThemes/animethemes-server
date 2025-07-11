<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use App\Filament\Actions\BaseAction;
use App\Filament\RelationManagers\BaseRelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class StorageAction.
 */
abstract class StorageAction extends BaseAction
{
    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (?Model $record, array $data) => $this->handle($record, $data));
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
     * Run this after the video is uploaded.
     *
     * @param  Model|null  $model
     * @param  array  $fields
     * @return void
     */
    protected function afterUploaded(?Model $model, array $fields): void {}

    /**
     * Perform the action on the given models.
     *
     * @param  Model|null  $model
     * @param  array  $fields
     * @return void
     */
    public function handle(?Model $model, array $fields): void
    {
        $action = $this->storageAction($model, $fields);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $model ??= $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());

            return;
        }

        $this->afterUploaded($model, $fields);

        $livewire = $this->getLivewire();
        if ($livewire instanceof BaseRelationManager && $model instanceof Model) {
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
