<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\Admin\ActionLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait HasPivotActionLogs.
 */
trait HasPivotActionLogs
{
    /**
     * Create the pivot action log.
     *
     * @param  string  $actionName
     * @param  BaseRelationManager $livewire
     * @param  Model  $record
     * @return void
     */
    public function pivotActionLog(string $actionName, BaseRelationManager $livewire, Model $record): void
    {
        $ownerRecord = $livewire->getOwnerRecord();

        /** @var BelongsToMany */
        $relation = $livewire->getRelationship();
        $pivotClass = $relation->getPivotClass();

        $pivot = $pivotClass::query()
            ->where($ownerRecord->getKeyName(), $ownerRecord->getKey())
            ->where($record->getKeyName(), $record->getKey())
            ->first();

        ActionLog::modelPivot(
            $actionName,
            $livewire->getOwnerRecord(),
            $record,
            $pivot,
        );
    }
}
