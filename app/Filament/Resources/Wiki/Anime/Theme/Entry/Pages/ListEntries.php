<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Theme\Entry\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime\Theme\EntryResource;
use App\Filament\Tabs\Anime\Theme\Entry\AnimeThemeEntryVideoTab;
use App\Filament\Tabs\Anime\Theme\Entry\Resource\AnimeThemeEntryYoutubeResourceTab;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListEntries extends BaseListResources
{
    use HasTabs;

    protected static string $resource = EntryResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, AnimeThemeEntry::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            AnimeThemeEntryYoutubeResourceTab::class,
            AnimeThemeEntryVideoTab::class,
        ]);
    }
}
