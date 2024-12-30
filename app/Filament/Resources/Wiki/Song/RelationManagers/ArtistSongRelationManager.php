<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Filament\RelationManagers\Wiki\ArtistRelationManager;
use App\Models\Wiki\Song;
use App\Models\Wiki\Artist;
use App\Pivots\Wiki\ArtistSong;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Table;

/**
 * Class ArtistSongRelationManager.
 */
class ArtistSongRelationManager extends ArtistRelationManager
{
    /**
     * Get the pivot fields of the relation.
     *
     * @return array<int, Component>
     */
    public function getPivotFields(): array
    {
        return [
            TextInput::make(ArtistSong::ATTRIBUTE_AS)
                ->label(__('filament.fields.artist.songs.as.name'))
                ->helperText(__('filament.fields.artist.songs.as.help')),

            TextInput::make(ArtistSong::ATTRIBUTE_ALIAS)
                ->label(__('filament.fields.artist.songs.alias.name'))
                ->helperText(__('filament.fields.artist.songs.alias.help')),
        ];
    }

    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Song::RELATION_ARTISTS;

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
                ->inverseRelationship(Artist::RELATION_SONGS)
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
            parent::getFilters(),
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
            [],
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
            [],
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
            [],
        );
    }
}
