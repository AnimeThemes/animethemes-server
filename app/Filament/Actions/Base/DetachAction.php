<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\DetachAction as BaseDetachAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class DetachAction extends BaseDetachAction
{
    use HasPivotActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament.actions.base.detach'));

        $this->visible(function (BaseManageResources|BaseViewResource|BaseListResources|BaseRelationManager $livewire) {
            if (! ($livewire instanceof BaseRelationManager)) {
                return false;
            }

            if (! ($livewire->getRelationship() instanceof BelongsToMany)) {
                return false;
            }

            $ownerRecord = $livewire->getOwnerRecord();

            $gate = Gate::getPolicyFor($ownerRecord);

            $model = Str::singular(class_basename($livewire->getTable()->getModel()));

            $detachAny = Str::of('detachAny')
                ->append($model)
                ->__toString();

            $detach = Str::of('detach')
                ->append($model)
                ->__toString();

            return is_object($gate) & method_exists($gate, $detachAny)
                ? Gate::forUser(Auth::user())->any([$detachAny, $detach], [$ownerRecord, $this->getRecord()])
                : true;
        });

        $this->after(function (BaseRelationManager $livewire, Model $record) {
            $relationship = $livewire->getRelationship();

            if ($relationship instanceof BelongsToMany) {
                $this->pivotActionLog('Detach', $livewire, $record);
            }
        });
    }
}
