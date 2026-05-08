<?php

declare(strict_types=1);

namespace App\Filament\Actions\Base;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Base\BaseManageResources;
use App\Filament\Resources\Base\BaseViewResource;
use Filament\Actions\EditAction as BaseEditAction;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

class EditAction extends BaseEditAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label(fn (): string => '');

        $this->icon(Heroicon::PencilSquare);
        $this->iconSize(IconSize::Medium);

        $this->schema(fn (Schema $schema, BaseListResources|BaseViewResource|BaseManageResources|BaseRelationManager $livewire): array => [
            ...$livewire->form($schema)->getComponents(),
            ...($livewire instanceof BaseRelationManager ? $livewire->getPivotComponents() : []),
        ]);

        $this->beforeFormFilled(function (Model $record): void {
            Gate::authorize('update', $record);
        });

        $this->authorize(true);
    }
}
