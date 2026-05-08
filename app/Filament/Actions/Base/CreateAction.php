<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use Filament\Actions\CreateAction as BaseCreateAction;
use Filament\Facades\Filament;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;

class CreateAction extends BaseCreateAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->schema(fn (Schema $schema, BaseManageResources|BaseListResources|BaseRelationManager $livewire): array => [
            ...$livewire->form($schema)->getComponents(),
            ...($livewire instanceof BaseRelationManager ? $livewire->getPivotComponents() : []),
        ]);

        $this->successRedirectUrl(function (Model $record, BaseManageResources|BaseListResources|BaseRelationManager $livewire) {
            if ($livewire instanceof BaseListResources) {
                return Filament::getModelResource($record)::getUrl('view', ['record' => $record]);
            }

            return null;
        });

        $this->visible(function (BaseManageResources|BaseListResources|BaseRelationManager $livewire, string $model): bool {
            if ($livewire instanceof BaseRelationManager && $livewire->getRelationship() instanceof BelongsToMany) {
                return false;
            }

            if ($livewire instanceof BaseListResources || $livewire instanceof BaseManageResources) {
                return $livewire->getResource()::canCreate() && Gate::allows('create', $model);
            }

            return $livewire->canCreate();
        });
    }
}
