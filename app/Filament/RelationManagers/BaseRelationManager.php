<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers;

use App\Filament\Actions\Base\AttachAction;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\BulkActions\Base\DetachBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Filament\Resources\BaseResource;
use App\Pivots\BasePivot;
use DateTime;
use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

abstract class BaseRelationManager extends RelationManager
{
    protected static bool $isLazy = false;

    protected $listeners = ['updateAllRelationManager' => '$refresh'];

    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = null;

    /**
     * @return Component[]
     */
    public function getPivotComponents(): array
    {
        return [];
    }

    /**
     * @return Column[]
     */
    public function getPivotColumns(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        $resource = static::$relatedResource;

        return $table
            ->columns([
                ...($resource ? $resource::table(Table::make($this))->getColumns() : []),

                ...$this->getPivotColumns(),

                TextColumn::make('pivot.created_at')
                    ->label(__('filament.fields.base.attached_at'))
                    ->hidden(fn ($livewire) => ! ($livewire->getRelationship() instanceof BelongsToMany))
                    ->state(function (Model $record) {
                        $pivot = current($record->getRelations());

                        $createdAtField = Arr::get($pivot->getAttributes(), BasePivot::ATTRIBUTE_CREATED_AT);
                        if (! $createdAtField) {
                            return '-';
                        }

                        return new DateTime($createdAtField)->format('M j, Y H:i:s');
                    }),

                TextColumn::make('pivot.updated_at')
                    ->label(__('filament.fields.base.updated_at'))
                    ->hidden(fn ($livewire) => ! ($livewire->getRelationship() instanceof BelongsToMany))
                    ->state(function (Model $record) {
                        $pivot = current($record->getRelations());
                        $updatedAtField = Arr::get($pivot->getAttributes(), BasePivot::ATTRIBUTE_UPDATED_AT);
                        if (! $updatedAtField) {
                            return '-';
                        }

                        return new DateTime($updatedAtField)->format('M j, Y H:i:s');
                    }),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with($resource ? $resource::getEloquentQuery()->getEagerLoads() : []))
            ->heading($resource ? $resource::getPluralModelLabel() : null)
            ->modelLabel($resource ? $resource::getModelLabel() : null)
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->recordActions(static::getRecordActions())
            ->toolbarActions(static::getBulkActions())
            ->headerActions(static::getHeaderActions())
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }

    /**
     * @return \Filament\Tables\Filters\BaseFilter[]
     */
    public static function getFilters(): array
    {
        return static::$relatedResource::getFilters();
    }

    /**
     * @return Action[]
     */
    public static function getRecordActions(): array
    {
        return [
            ...(static::$relatedResource ? static::$relatedResource::getActions() : []),
        ];
    }

    /**
     * @param  array<int, \Filament\Actions\ActionGroup|Action>|null  $actionsIncludedInGroup
     * @return array<int, \Filament\Actions\ActionGroup|Action>
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        $relatedResource = static::$relatedResource;

        return [
            ...($relatedResource ? $relatedResource::getBulkActions() : []),

            DetachBulkAction::make(),
        ];
    }

    /**
     * @return Action[]
     */
    public static function getHeaderActions(): array
    {
        $relatedResource = static::$relatedResource;

        return [
            CreateAction::make(),

            AttachAction::make(),

            ...($relatedResource ? $relatedResource::getTableActions() : []),
        ];
    }

    public function canCreate(): bool
    {
        return true;
    }
}
