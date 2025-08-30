<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\Theme\EntryRelationManager;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use Filament\Tables\Table;

class EntryVideoRelationManager extends EntryRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Video::RELATION_ANIMETHEMEENTRIES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(AnimeThemeEntry::RELATION_VIDEOS)
        );
    }
}
