<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\ThemeRelationManager;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Song as SongModel;
use Filament\Tables\Table;

class ThemeSongRelationManager extends ThemeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = SongModel::RELATION_ANIMETHEMES;

    /**
     * The index page of the resource.
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ThemeModel::RELATION_SONG)
        );
    }
}
