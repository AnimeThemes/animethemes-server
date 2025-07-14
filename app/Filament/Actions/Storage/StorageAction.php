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
     * @param  Model  $record
     * @param  array<string, mixed>  $data
     * @return BaseStorageAction
     */
    abstract protected function storageAction(Model $record, array $data): BaseStorageAction;

    /**
     * Run this after the video is uploaded.
     *
     * @param  Model|null  $record
     * @param  array<string, mixed>  $data
     * @return void
     */
    protected function afterUploaded(?Model $record, array $data): void {}

    /**
     * Perform the action on the given models.
     *
     * @param  Model|null  $record
     * @param  array<string, mixed>  $data
     * @return void
     */
    public function handle(?Model $record, array $data): void
    {
        $action = $this->storageAction($record, $data);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $record ??= $action->then($storageResults);

        $actionResult = $storageResults->toActionResult();

        if ($actionResult->hasFailed()) {
            $this->failedLog($actionResult->getMessage());

            return;
        }

        $this->afterUploaded($record, $data);

        $livewire = $this->getLivewire();
        if ($livewire instanceof BaseRelationManager && $record instanceof Model) {
            $relation = $livewire->getRelationship();
            $pivot = $record;

            if ($relation instanceof BelongsToMany) {
                $pivotClass = $relation->getPivotClass();

                $pivot = $pivotClass::query()
                    ->where($livewire->getOwnerRecord()->getKeyName(), $livewire->getOwnerRecord()->getKey())
                    ->where($record->getKeyName(), $record->getKey())
                    ->first();
            }

            $this->updateLog($record, $pivot);
        }
    }
}
