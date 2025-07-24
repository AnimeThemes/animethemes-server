<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\RelationManagers;

use App\Filament\RelationManagers\List\Playlist\TrackRelationManager;
use App\Models\List\Playlist\PlaylistTrack;
use App\Models\Wiki\Video;
use Filament\Tables\Table;

class TrackVideoRelationManager extends TrackRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Video::RELATION_TRACKS;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(PlaylistTrack::RELATION_VIDEO)
        );
    }
}
