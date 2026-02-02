<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\ThemeRelationManager;
use App\Models\Wiki\Anime;
use App\Models\Wiki\Anime\AnimeTheme;
use Filament\Tables\Table;

class ThemeAnimeRelationManager extends ThemeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Anime::RELATION_THEMES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(AnimeTheme::RELATION_ANIME)
        );
    }
}
