<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Entry\RelationManagers;

use App\Filament\RelationManagers\Wiki\VideoRelationManager;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Filament\Tables\Table;

class VideoEntryRelationManager extends VideoRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = AnimeThemeEntry::RELATION_VIDEOS;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Video::RELATION_ANIMETHEMEENTRIES)
        );
    }
}
