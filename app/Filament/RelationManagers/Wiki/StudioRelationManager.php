<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Studio as StudioResource;
use App\Models\Wiki\Studio;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class StudioRelationManager.
 */
abstract class StudioRelationManager extends BaseRelationManager
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
        return StudioResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(StudioResource::getEloquentQuery()->getEagerLoads()))
                ->heading(StudioResource::getPluralLabel())
                ->modelLabel(StudioResource::getLabel())
                ->recordTitleAttribute(Studio::ATTRIBUTE_NAME)
                ->columns(StudioResource::table($table)->getColumns())
                ->defaultSort(Studio::TABLE.'.'.Studio::ATTRIBUTE_ID, 'desc')
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
            ...StudioResource::getFilters(),
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
            ...StudioResource::getActions(),
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
            ...StudioResource::getBulkActions(),
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
            ...StudioResource::getTableActions(),
        ];
    }
}
