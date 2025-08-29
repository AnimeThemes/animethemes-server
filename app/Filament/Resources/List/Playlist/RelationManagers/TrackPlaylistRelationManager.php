<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\RelationManagers;

use App\Filament\RelationManagers\List\Playlist\TrackRelationManager;
use App\Models\List\Playlist;
use App\Models\List\Playlist\PlaylistTrack;
use Filament\Tables\Table;

class TrackPlaylistRelationManager extends TrackRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Playlist::RELATION_TRACKS;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(PlaylistTrack::RELATION_PLAYLIST)
        );
    }
}
