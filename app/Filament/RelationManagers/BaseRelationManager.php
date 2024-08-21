<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers;

use App\Filament\Actions\Base\AttachAction;
use App\Filament\Actions\Base\CreateAction;
use App\Filament\BulkActions\Base\DetachBulkAction;
use App\Filament\Components\Columns\TextColumn;
use App\Pivots\BasePivot;
use DateTime;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
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
     * The index page of the relation resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns(array_merge(
                $table->getColumns(),
                [
                    TextColumn::make(BasePivot::ATTRIBUTE_CREATED_AT)
                        ->label(__('filament.fields.base.created_at'))
                        ->hidden(fn ($livewire) => !($livewire->getRelationship() instanceof BelongsToMany))
                        ->formatStateUsing(function ($record) {
                            $pivot = current($record->getRelations());
                            $createdAtField = Arr::get($pivot->getAttributes(), BasePivot::ATTRIBUTE_CREATED_AT);
                            if (!$createdAtField) return '-';
                            return (new DateTime($createdAtField))->format('M j, Y H:i:s');
                        }),

                    TextColumn::make(BasePivot::ATTRIBUTE_UPDATED_AT)
                        ->label(__('filament.fields.base.updated_at'))
                        ->hidden(fn ($livewire) => !($livewire->getRelationship() instanceof BelongsToMany))
                        ->formatStateUsing(function ($record) {
                            $pivot = current($record->getRelations());
                            $updatedAtField = Arr::get($pivot->getAttributes(), BasePivot::ATTRIBUTE_UPDATED_AT);
                            if (!$updatedAtField) return '-';
                            return (new DateTime($updatedAtField))->format('M j, Y H:i:s');
                        }),
                ],
            ))
            ->filters(static::getFilters())
            ->filtersFormMaxHeight('400px')
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions())
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
        return [
            TrashedFilter::make(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
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
     * Get the header actions available for the relation.
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
