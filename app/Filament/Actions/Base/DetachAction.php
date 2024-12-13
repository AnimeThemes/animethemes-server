<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use Filament\Tables\Actions\DetachAction as DefaultDetachAction;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

/**
 * Class DetachAction.
 */
class DetachAction extends DefaultDetachAction
{
    use HasPivotActionLogs;

    /**
     * Initial setup for the action.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.detach'));

        $this->visible(function (mixed $livewire) {
            if (
                !($livewire instanceof BaseRelationManager)
                || !($livewire->getRelationship() instanceof BelongsToMany)
            ) {
                return false;
            }

            $ownerRecord = $livewire->getOwnerRecord();

            $gate = Gate::getPolicyFor($ownerRecord);

            $model = Str::singular(class_basename($livewire->getTable()->getModel()));

            $detachAny = Str::of('detachAny')
                ->append($model)
                ->toString();

            $detach = Str::of('detach')
                ->append($model)
                ->toString();

            return is_object($gate) & method_exists($gate, $detachAny)
                ? Gate::forUser(Auth::user())->any([$detachAny, $detach], [$ownerRecord, $this->getRecord()])
                : true;
        });

        $this->after(function ($livewire, $record) {
            $relationship = $livewire->getRelationship();

            if ($relationship instanceof BelongsToMany) {
                $this->pivotActionLog('Detach', $livewire, $record);
            }
        });
    }
}
