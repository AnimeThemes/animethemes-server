<?php

declare(strict_types=1);

namespace App\Concerns\Filament\Actions;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Models\Admin\ActionLog;
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
     * @param  BaseRelationManager $livewire
     * @param  Model  $record
     * @return void
     */
    public function pivotActionLog(string $actionName, BaseRelationManager $livewire, Model $record): void
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
            );
        }
    }

    /**
     * Create the associate action log.
     *
     * @param  string  $actionName
     * @param  BaseRelationManager $livewire
     * @param  Model  $record
     * @return void
     */
    public function associateActionLog(string $actionName, BaseRelationManager $livewire, Model $record): void
    {
        $ownerRecord = $livewire->getOwnerRecord();

        $relation = $livewire->getRelationship();

        if ($relation instanceof HasMany) {
            ActionLog::modelAssociated(
                $actionName,
                $ownerRecord,
                $record,
            );
        }
    }
}
