<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Song;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Song\Performance as PerformanceResource;
use App\Models\Wiki\Song\Performance;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PerformanceRelationManager.
 */
abstract class PerformanceRelationManager extends BaseRelationManager
{
    /**
     * The form to the actions.
     *
     * @param  Schema  $schema
     * @return Schema
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Schema $schema): Schema
    {
        return PerformanceResource::form($schema);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->modifyQueryUsing(fn (Builder $query) => $query->with(PerformanceResource::getEloquentQuery()->getEagerLoads()))
                ->heading(PerformanceResource::getPluralLabel())
                ->modelLabel(PerformanceResource::getLabel())
                ->recordTitleAttribute(Performance::ATTRIBUTE_ID)
                ->columns(PerformanceResource::table($table)->getColumns())
                ->defaultSort(Performance::TABLE . '.' . Performance::ATTRIBUTE_ID, 'desc')
        );
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
            ...PerformanceResource::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
            ...PerformanceResource::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
            ...PerformanceResource::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
            ...PerformanceResource::getTableActions(),
        ];
    }
}
