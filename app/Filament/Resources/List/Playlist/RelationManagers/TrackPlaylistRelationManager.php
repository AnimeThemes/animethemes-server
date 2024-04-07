<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Playlist\RelationManagers;

use App\Filament\Resources\BaseRelationManager;
use App\Filament\Resources\List\Playlist\Track as TrackResource;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class TrackPlaylistRelationManager.
 */
class TrackPlaylistRelationManager extends BaseRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @return string
     */
    protected static string $relationship = Playlist::RELATION_TRACKS;

    /**
     * The form to the actions.
     *
     * @param  Form  $form
     * @return Form
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function form(Form $form): Form
    {
        return TrackResource::form($form);
    }

    /**
     * The index page of the resource.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute(PlaylistTrack::ATTRIBUTE_HASHID)
            ->inverseRelationship(PlaylistTrack::RELATION_PLAYLIST)
            ->columns(TrackResource::table($table)->getColumns())
            ->defaultSort(PlaylistTrack::TABLE.'.'.PlaylistTrack::ATTRIBUTE_ID, 'desc')
            ->filters(static::getFilters())
            ->headerActions(static::getHeaderActions())
            ->actions(static::getActions())
            ->bulkActions(static::getBulkActions());
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
        return array_merge(
            parent::getFilters(),
            [],
        );
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
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the relation.
     * 
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
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
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
