<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\Theme\EntryRelationManager;
use App\Models\Wiki\Anime\AnimeTheme;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Filament\Tables\Table;

/**
 * Class EntryThemeRelationManager.
 */
class EntryThemeRelationManager extends EntryRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = AnimeTheme::RELATION_ENTRIES;

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
                ->inverseRelationship(AnimeThemeEntry::RELATION_THEME)
        );
    }
}
