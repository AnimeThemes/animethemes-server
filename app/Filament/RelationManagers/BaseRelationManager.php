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
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Component;
use Filament\Tables\Columns\Column;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * Class BaseRelationManager.
 */
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
     * Get the pivot components of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotComponents(): array
    {
        return [];
    }

    /**
     * Get the pivot columns of the relation.
     *
     * @return array<int, Column>
     */
    public function getPivotColumns(): array
    {
        return [];
    }

    /**
     * The index page of the relation resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        $resource = static::$relatedResource;

        return $table
            ->columns([
                ...$table->getColumns(),

                ...$this->getPivotColumns(),

                TextColumn::make(BasePivot::ATTRIBUTE_CREATED_AT)
                    ->label(__('filament.fields.base.attached_at'))
                    ->hidden(fn ($livewire) => ! ($livewire->getRelationship() instanceof BelongsToMany))
                    ->formatStateUsing(function (Model $record) {
                        $pivot = current($record->getRelations());
                        $createdAtField = Arr::get($pivot->getAttributes(), BasePivot::ATTRIBUTE_CREATED_AT);
                        if (! $createdAtField) {
                            return '-';
                        }

                        return new DateTime($createdAtField)->format('M j, Y H:i:s');
                    }),

                TextColumn::make(BasePivot::ATTRIBUTE_UPDATED_AT)
                    ->label(__('filament.fields.base.updated_at'))
                    ->hidden(fn ($livewire) => ! ($livewire->getRelationship() instanceof BelongsToMany))
                    ->formatStateUsing(function (Model $record) {
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
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return static::$relatedResource::getFilters();
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            DetachBulkAction::make(),
        ];
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            AttachAction::make(),
        ];
    }
}
