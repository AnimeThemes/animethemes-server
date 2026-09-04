<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Entry\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\EntryResource;
use App\Filament\Tabs\Entry\EntryVideoTab;
use App\Filament\Tabs\Entry\Resource\EntryYoutubeResourceTab;
use App\Models\Wiki\Entry;
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
        return $this->makeScout($query, Entry::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            EntryYoutubeResourceTab::class,
            EntryVideoTab::class,
        ]);
    }
}
