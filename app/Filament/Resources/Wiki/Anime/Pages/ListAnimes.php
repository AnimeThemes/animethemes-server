<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Anime\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Anime;
use App\Filament\Tabs\Anime\AnimeStreamingResourceTab;
use App\Filament\Tabs\Anime\Image\AnimeCoverLargeTab;
use App\Filament\Tabs\Anime\Image\AnimeCoverSmallTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnidbResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnilistResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeAnnResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeKitsuResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeMalResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeOfficialSiteResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimePlanetResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeTwitterResourceTab;
use App\Filament\Tabs\Anime\Resource\AnimeYoutubeResourceTab;
use App\Filament\Tabs\Anime\Studio\AnimeStudioTab;
use App\Models\Wiki\Anime as AnimeModel;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListAnimes.
 */
class ListAnimes extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Anime::class;

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
            $query->whereIn(AnimeModel::ATTRIBUTE_ID, AnimeModel::search($search)->take(25)->keys());
        }

        return $query;
    }

    /**
     * Get the tabs available.
     *
     * @return array
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            AnimeCoverLargeTab::class,
            AnimeCoverSmallTab::class,
            AnimeAnidbResourceTab::class,
            AnimeAnilistResourceTab::class,
            AnimeAnnResourceTab::class,
            AnimeKitsuResourceTab::class,
            AnimeMalResourceTab::class,
            AnimeOfficialSiteResourceTab::class,
            AnimePlanetResourceTab::class,
            AnimeTwitterResourceTab::class,
            AnimeYoutubeResourceTab::class,
            AnimeStreamingResourceTab::class,
            AnimeStudioTab::class,
        ]);
    }
}
