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
                ->defaultSort(AnimeThemeEntry::TABLE.'.'.AnimeThemeEntry::ATTRIBUTE_ID, 'desc')
        );
    }
}
