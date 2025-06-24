<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\Wiki\Artist as ArtistResource;
use App\Models\Wiki\Artist;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ArtistRelationManager.
 */
abstract class ArtistRelationManager extends BaseRelationManager
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
        return ArtistResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(ArtistResource::getEloquentQuery()->getEagerLoads()))
                ->heading(ArtistResource::getPluralLabel())
                ->modelLabel(ArtistResource::getLabel())
                ->recordTitleAttribute(Artist::ATTRIBUTE_NAME)
                ->columns(ArtistResource::table($table)->getColumns())
                ->defaultSort(Artist::TABLE.'.'.Artist::ATTRIBUTE_ID, 'desc')
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
            ...ArtistResource::getFilters(),
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
            ...ArtistResource::getRecordActions(),
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
            ...ArtistResource::getBulkActions(),
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
            ...ArtistResource::getTableActions(),
        ];
    }
}
