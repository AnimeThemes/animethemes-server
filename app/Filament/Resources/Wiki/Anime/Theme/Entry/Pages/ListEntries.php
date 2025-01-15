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
     * Get the header actions available.
     *
     * @return array
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    protected function getHeaderActions(): array
    {
        return array_merge(
            parent::getHeaderActions(),
            [],
        );
    }

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        $this->applyColumnSearchesToTableQuery($query);

        if (filled($search = $this->getTableSearch())) {
            $search = preg_replace('/[^A-Za-z0-9 ]/', '', $search);
            $query->whereIn(AnimeThemeEntry::ATTRIBUTE_ID, AnimeThemeEntry::search($search)->take(25)->keys());
        }

        return $query;
    }
}
