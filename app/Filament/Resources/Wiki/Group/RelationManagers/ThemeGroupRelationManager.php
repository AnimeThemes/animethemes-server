<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\ThemeRelationManager;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Group;
use Filament\Tables\Table;

class ThemeGroupRelationManager extends ThemeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Group::RELATION_THEMES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(AnimeTheme::RELATION_GROUP)
        );
    }
}
