<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List;

use Filament\Schemas\Schema;
use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\List\Playlist as PlaylistResource;
use App\Models\List\Playlist;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class PlaylistRelationManager.
 */
abstract class PlaylistRelationManager extends BaseRelationManager
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
        return PlaylistResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(PlaylistResource::getEloquentQuery()->getEagerLoads()))
                ->heading(PlaylistResource::getPluralLabel())
                ->modelLabel(PlaylistResource::getLabel())
                ->recordTitleAttribute(Playlist::ATTRIBUTE_NAME)
                ->columns(PlaylistResource::table($table)->getColumns())
                ->defaultSort(Playlist::TABLE.'.'.Playlist::ATTRIBUTE_ID, 'desc')
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
            ...PlaylistResource::getFilters(),
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
            ...PlaylistResource::getActions(),
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
            ...PlaylistResource::getBulkActions(),
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
            ...PlaylistResource::getTableActions(),
        ];
    }
}
