<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Theme\RelationManagers;

use App\Filament\RelationManagers\Wiki\ThemeStaffRelationManager;
use App\Models\Wiki\Theme;
use App\Models\Wiki\ThemeStaff;
use Filament\Tables\Table;

class ThemeStaffThemeRelationManager extends ThemeStaffRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     */
    protected static string $relationship = Theme::RELATION_STAFF;

    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ThemeStaff::RELATION_THEME)
        )
            ->heading(__('filament.resources.label.staff'))
            ->modelLabel(__('filament.resources.singularLabel.staff'));
    }
}
