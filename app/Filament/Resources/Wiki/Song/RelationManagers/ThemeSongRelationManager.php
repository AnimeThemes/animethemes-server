<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\ThemeRelationManager;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Song;
use Filament\Tables\Table;

class ThemeSongRelationManager extends ThemeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Song::RELATION_ANIMETHEMES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(AnimeTheme::RELATION_SONG)
        );
    }
}
