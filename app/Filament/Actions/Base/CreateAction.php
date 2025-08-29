<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Concerns\Filament\ActionLogs\HasPivotActionLogs;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\RelationManagers\Wiki\ResourceRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Models\Admin\ActionLog;
use Filament\Actions\CreateAction as BaseCreateAction;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CreateAction extends BaseCreateAction
{
    use HasPivotActionLogs;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(fn (Schema $schema, BaseManageResources|BaseListResources|BaseRelationManager $livewire) => [
            ...$livewire->form($schema)->getComponents(),
            ...($livewire instanceof BaseRelationManager ? $livewire->getPivotComponents() : []),
        ]);

        $this->successRedirectUrl(function (Model $record, BaseManageResources|BaseListResources|BaseRelationManager $livewire) {
            if ($livewire instanceof BaseListResources) {
                return Filament::getModelResource($record)::getUrl('view', ['record' => $record]);
            }

            return null;
        });

        $this->after(function (BaseManageResources|BaseListResources|BaseRelationManager $livewire, Model $record, CreateAction $action) {
            if ($livewire instanceof BaseListResources) {
                ActionLog::modelCreated($record);
            }

            if ($livewire instanceof BaseRelationManager) {
                $relationship = $livewire->getRelationship();

                if ($relationship instanceof HasMany) {
                    $this->associateActionLog('Create and Associate', $livewire, $record, $action);
                }
            }
        });

        $this->visible(function (BaseManageResources|BaseListResources|BaseRelationManager $livewire, string $model) {
            if ($livewire instanceof BaseListResources || $livewire instanceof BaseManageResources) {
                return $livewire->getResource()::canCreate() && Gate::allows('create', $model);
            }

            if ($livewire instanceof ResourceRelationManager) {
                return $livewire->canCreate();
            }

            if (! $livewire->canCreate()) {
                return false;
            }

            if ($livewire->getRelationship() instanceof BelongsToMany) {
                return false;
            }

            $ownerRecord = $livewire->getOwnerRecord();

            $gate = Gate::getPolicyFor($ownerRecord);

            $ability = Str::of('addAny')
                ->append(Str::singular(class_basename($livewire->getTable()->getModel())))
                ->__toString();

            return is_object($gate) & method_exists($gate, $ability)
                ? Gate::forUser(Auth::user())->any($ability, $ownerRecord)
                : true;
        });
    }
}
