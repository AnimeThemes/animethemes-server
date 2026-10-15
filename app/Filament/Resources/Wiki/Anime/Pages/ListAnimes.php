<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\AnimeResource;
use App\Filament\Tabs\Anime\AnimeImageTab;
use App\Filament\Tabs\Anime\AnimeThemesTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnidbResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnilistResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeMalResourceTab;
use App\Filament\Tabs\Anime\Studio\AnimeStudioTab;
use App\Models\Wiki\Anime;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListAnimes extends BaseListResources
{
    use HasTabs;

    protected static string $resource = AnimeResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Anime::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            AnimeThemesTab::class,
            AnimeImageTab::class,
            AnimeAnidbResourceTab::class,
            AnimeAnilistResourceTab::class,
            AnimeMalResourceTab::class,
            AnimeStudioTab::class,
        ]);
    }
}
