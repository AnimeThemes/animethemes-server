<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\List\Playlist;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\List\Playlist\Track as TrackResource;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Forms\Form;
use Filament\Tables\Table;

/**
 * Class TrackRelationManager.
 */
abstract class TrackRelationManager extends BaseRelationManager
{
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
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->heading(TrackResource::getPluralLabel())
                ->modelLabel(TrackResource::getLabel())
                ->recordTitleAttribute(PlaylistTrack::ATTRIBUTE_HASHID)
                ->columns(TrackResource::table($table)->getColumns())
                ->defaultSort(PlaylistTrack::TABLE . '.' . PlaylistTrack::ATTRIBUTE_ID, 'desc')
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
        return array_merge(
            [],
            TrackResource::getFilters(),
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            TrackResource::getActions(),
        );
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            TrackResource::getBulkActions(),
        );
    }

    /**
     * Get the header actions available for the relation. These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            TrackResource::getTableActions(),
        );
    }
}
