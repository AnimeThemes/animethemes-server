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

    /**
     * Get the filters available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getFilters(): array
    {
        return [
            ...parent::getFilters(),
        ];
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getActions(): array
    {
        return [
            ...parent::getActions(),
        ];
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return [
            ...parent::getBulkActions(),
        ];
    }

    /**
     * Get the header actions available for the relation.
     * These are merged with the table actions of the resources.
     *
     * @return array
     */
    public static function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
        ];
    }
}
