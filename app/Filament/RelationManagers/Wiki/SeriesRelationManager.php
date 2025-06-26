<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Series as SeriesResource;
use App\Models\Wiki\Series;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SeriesRelationManager.
 */
abstract class SeriesRelationManager extends BaseRelationManager
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
        return SeriesResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(SeriesResource::getEloquentQuery()->getEagerLoads()))
                ->heading(SeriesResource::getPluralLabel())
                ->modelLabel(SeriesResource::getLabel())
                ->recordTitleAttribute(Series::ATTRIBUTE_NAME)
                ->columns(SeriesResource::table($table)->getColumns())
                ->defaultSort(Series::TABLE . '.' . Series::ATTRIBUTE_ID, 'desc')
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
            ...SeriesResource::getFilters(),
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
            ...SeriesResource::getActions(),
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
            ...SeriesResource::getBulkActions(),
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
            ...SeriesResource::getTableActions(),
        ];
    }
}
