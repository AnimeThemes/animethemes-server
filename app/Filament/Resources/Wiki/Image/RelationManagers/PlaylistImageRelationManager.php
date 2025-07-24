<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Image\RelationManagers;

use App\Filament\RelationManagers\List\PlaylistRelationManager;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use Filament\Tables\Table;

class PlaylistImageRelationManager extends PlaylistRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Image::RELATION_PLAYLISTS;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Playlist::RELATION_IMAGES)
        );
    }
}
