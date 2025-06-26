<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use Filament\Schemas\Components\Tabs\Tab;
use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime;
use App\Filament\Tabs\Anime\AnimeStreamingResourceTab;
use App\Filament\Tabs\Anime\Image\AnimeLargeCoverTab;
use App\Filament\Tabs\Anime\Image\AnimeSmallCoverTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnidbResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnilistResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnnResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeKitsuResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeMalResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeOfficialSiteResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimePlanetResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeXResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeYoutubeResourceTab;
use App\Filament\Tabs\Anime\Studio\AnimeStudioTab;
use App\Models\Wiki\Anime as AnimeModel;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListAnimes.
 */
class ListAnimes extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Anime::class;

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, AnimeModel::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            AnimeLargeCoverTab::class,
            AnimeSmallCoverTab::class,
            AnimeAnidbResourceTab::class,
            AnimeAnilistResourceTab::class,
            AnimeAnnResourceTab::class,
            AnimeKitsuResourceTab::class,
            AnimeMalResourceTab::class,
            AnimeOfficialSiteResourceTab::class,
            AnimePlanetResourceTab::class,
            AnimeXResourceTab::class,
            AnimeYoutubeResourceTab::class,
            AnimeStreamingResourceTab::class,
            AnimeStudioTab::class,
        ]);
    }
}
