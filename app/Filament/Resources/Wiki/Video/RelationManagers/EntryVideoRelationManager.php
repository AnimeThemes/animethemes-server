<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Video\RelationManagers;

use App\Filament\RelationManagers\Wiki\Anime\Theme\EntryRelationManager;
use App\Models\Wiki\Video;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Filament\Tables\Table;

/**
 * Class EntryVideoRelationManager.
 */
class EntryVideoRelationManager extends EntryRelationManager
{
    /**
     * The relationship the relation manager corresponds to.
     *
     * @var string
     */
    protected static string $relationship = Video::RELATION_ANIMETHEMEENTRIES;

    /**
     * The index page of the resource.
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
                ->inverseRelationship(AnimeThemeEntry::RELATION_VIDEOS)
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
        return array_merge(
            [],
            parent::getFilters(),
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getActions(): array
    {
        return array_merge(
            parent::getActions(),
            [],
        );
    }

    /**
     * Get the bulk actions available for the relation.
     *
     * @param  array|null  $actionsIncludedInGroup
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getBulkActions(?array $actionsIncludedInGroup = []): array
    {
        return array_merge(
            parent::getBulkActions(),
            [],
        );
    }

    /**
     * Get the header actions available for the relation.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }
}
