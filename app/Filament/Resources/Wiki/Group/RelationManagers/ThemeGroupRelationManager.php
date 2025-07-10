<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Group\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\ThemeRelationManager;
use App\Models\Wiki\Anime\AnimeTheme as ThemeModel;
use App\Models\Wiki\Group;
use Filament\Tables\Table;

/**
 * Class ThemeGroupRelationManager.
 */
class ThemeGroupRelationManager extends ThemeRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Group::RELATION_THEMES;

    /**
     * The index page of the Theme.
     *
     * @param  Table  $table
     * @return Table
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function table(Table $table): Table
    {
        return parent::table(
            $table
                ->inverseRelationship(ThemeModel::RELATION_GROUP)
        );
    }
}
