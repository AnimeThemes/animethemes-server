<?php

declare(strict_types=1);

namespace App\Concerns\Filament\ActionLogs;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\Admin\ActionLog;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasPivotActionLogs
{
    /**
     * Create the pivot action log.
     */
    public function pivotActionLog(string $actionName, BaseRelationManager $livewire, Model $record, ?Action $action = null): void
    {
        $ownerRecord = $livewire->getOwnerRecord();

        $relation = $livewire->getRelationship();

        if ($relation instanceof BelongsToMany) {
            $pivotClass = $relation->getPivotClass();

            $pivot = $pivotClass::query()
                ->where($relation->getForeignPivotKeyName(), $ownerRecord->getKey())
                ->where($relation->getRelatedPivotKeyName(), $record->getKey())
                ->first();

            ActionLog::modelPivot(
                $actionName,
                $ownerRecord,
                $record,
                $pivot ?? $record,
                $action,
            );
        }
    }

    /**
     * Create the associate action log.
     */
    public function associateActionLog(string $actionName, BaseRelationManager $livewire, Model $record, Action $action): void
    {
        $ownerRecord = $livewire->getOwnerRecord();

        $relation = $livewire->getRelationship();

        if ($relation instanceof HasMany) {
            ActionLog::modelAssociated(
                $actionName,
                $ownerRecord,
                $record,
                $action,
            );
        }
    }
}
