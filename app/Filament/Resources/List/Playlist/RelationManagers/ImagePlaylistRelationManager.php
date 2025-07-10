<?php

declare(strict_types=1);

namespace App\Filament\Resources\List\Playlist\RelationManagers;

use App\Filament\RelationManagers\Wiki\ImageRelationManager;
use App\Models\List\Playlist;
use App\Models\Wiki\Image;
use Filament\Tables\Table;

/**
 * Class ImagePlaylistRelationManager.
 */
class ImagePlaylistRelationManager extends ImageRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Playlist::RELATION_IMAGES;

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
                ->inverseRelationship(Image::RELATION_PLAYLISTS)
        );
    }
}
