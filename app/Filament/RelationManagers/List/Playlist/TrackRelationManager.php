<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List\Playlist;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\List\Playlist\Track as TrackResource;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TrackRelationManager.
 */
abstract class TrackRelationManager extends BaseRelationManager
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
        return TrackResource::form($schema);
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
                ->modifyQueryUsing(fn (Builder $query) => $query->with(TrackResource::getEloquentQuery()->getEagerLoads()))
                ->heading(TrackResource::getPluralLabel())
                ->modelLabel(TrackResource::getLabel())
                ->recordTitleAttribute(PlaylistTrack::ATTRIBUTE_HASHID)
                ->columns(TrackResource::table($table)->getColumns())
                ->defaultSort(PlaylistTrack::TABLE.'.'.PlaylistTrack::ATTRIBUTE_ID, 'desc')
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
            ...TrackResource::getFilters(),
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
            ...TrackResource::getActions(),
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
            ...TrackResource::getBulkActions(),
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
            ...TrackResource::getTableActions(),
        ];
    }
}
