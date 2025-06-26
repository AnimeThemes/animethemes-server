<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages;

use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime\Theme\Entry;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListEntries.
 */
class ListEntries extends BaseListResources
{
    protected static string $resource = Entry::class;

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, AnimeThemeEntry::class);
    }
}
