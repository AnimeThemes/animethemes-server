<?php

declare(strict_types=1);

namespace App\Filament\RelationManagers\Wiki\Anime\Theme;

use App\Filament\RelationManagers\BaseRelationManager;
use App\Filament\Resources\BaseResource;
use App\Filament\Resources\Wiki\Anime\Theme\Entry as EntryResource;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Filament\Tables\Table;

/**
 * Class EntryRelationManager.
 */
abstract class EntryRelationManager extends BaseRelationManager
{
    /**
     * The resource of the relation manager.
     *
     * @var class-string<BaseResource>|null
     */
    protected static ?string $relatedResource = EntryResource::class;

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
                ->recordTitleAttribute(AnimeThemeEntry::ATTRIBUTE_VERSION)
                ->columns(EntryResource::table($table)->getColumns())
                ->defaultSort(AnimeThemeEntry::TABLE.'.'.AnimeThemeEntry::ATTRIBUTE_ID, 'desc')
        );
    }

    /**
     * Get the actions available for the relation.
     *
     * @return array
     */
    public static function getRecordActions(): array
    {
        return [
            ...parent::getRecordActions(),
            ...EntryResource::getActions(),
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
            ...EntryResource::getBulkActions(),
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
            ...EntryResource::getTableActions(),
        ];
    }
}
