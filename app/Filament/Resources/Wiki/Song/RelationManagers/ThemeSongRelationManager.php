<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\RelationManagers;

use App\Filament\RelationManagers\Wiki\ThemeRelationManager;
use App\Models\Wiki\Song;
use App\Models\Wiki\Theme;
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
                ->inverseRelationship(Theme::RELATION_SONG)
        );
    }
}
