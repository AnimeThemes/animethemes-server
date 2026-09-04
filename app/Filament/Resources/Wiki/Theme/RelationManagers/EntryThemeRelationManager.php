<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Theme\RelationManagers;

use App\Filament\RelationManagers\Wiki\EntryRelationManager;
use App\Models\Wiki\Entry;
use App\Models\Wiki\Theme;
use Filament\Tables\Table;

class EntryThemeRelationManager extends EntryRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Theme::RELATION_ENTRIES;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(Entry::RELATION_THEME)
        );
    }
}
