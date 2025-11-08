<?php

declare(strict_types=1);

namespace App\Filament\Actions\Storage;

use App\Contracts\Actions\Storage\StorageAction as BaseStorageAction;
use App\Filament\Actions\BaseAction;
use App\Filament\RelationManagers\BaseRelationManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

abstract class StorageAction extends BaseAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(fn (?Model $record, array $data) => $this->handle($record, $data));
    }

    /**
     * Get the underlying storage action.
     *
     * @param  array<string, mixed>  $data
     */
    abstract protected function storageAction(Model $record, array $data): BaseStorageAction;

    /**
     * Run this after the video is uploaded.
     *
     * @param  array<string, mixed>  $data
     */
    protected function afterUploaded(?Model $record, array $data): void {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(?Model $record, array $data): void
    {
        $action = $this->storageAction($record, $data);

        $storageResults = $action->handle();

        $storageResults->toLog();

        $updated = $action->then($storageResults);

        $record ??= $updated;

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
