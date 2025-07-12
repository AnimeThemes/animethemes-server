<?php

declare(strict_types=1);

namespace App\Concerns\Filament\ActionLogs;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\Admin\ActionLog;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Trait HasPivotActionLogs.
 */
trait HasPivotActionLogs
{
    /**
     * Create the pivot action log.
     *
     * @param  string  $actionName
     * @param  BaseRelationManager  $livewire
     * @param  Model  $record
     * @param  Action|null  $action
     * @return void
     */
    public function pivotActionLog(string $actionName, BaseRelationManager $livewire, Model $record, ?Action $action = null): void
    {
        $ownerRecord = $livewire->getOwnerRecord();

        $relation = $livewire->getRelationship();

        if ($relation instanceof BelongsToMany) {
            $pivotClass = $relation->getPivotClass();

            // TODO: This needs to be updated/fixed for member/group artist relation
            $pivot = $pivotClass::query()
                ->where($ownerRecord->getKeyName(), $ownerRecord->getKey())
                ->where($record->getKeyName(), $record->getKey())
                ->first();

            ActionLog::modelPivot(
                $actionName,
                $ownerRecord,
                $record,
                $pivot ?? $record,
                $action
            );
        }
    }

    /**
     * Create the associate action log.
     *
     * @param  string  $actionName
     * @param  BaseRelationManager  $livewire
     * @param  Model  $record
     * @param  Action  $action
     * @return void
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
