<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Artist;
use App\Filament\Tabs\Artist\Image\ArtistCoverLargeTab;
use App\Filament\Tabs\Artist\Image\ArtistCoverSmallTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnidbResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnilistResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnimePlanetResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnnResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistMalResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistOfficialSiteResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistSpotifyResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistXResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistYoutubeResourceTab;
use App\Filament\Tabs\Artist\Song\ArtistSongTab;
use App\Models\Wiki\Artist as ArtistModel;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListArtists.
 */
class ListArtists extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Artist::class;

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
            $query->whereIn(ArtistModel::ATTRIBUTE_ID, ArtistModel::search($search)->take(25)->keys());
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
            ArtistCoverLargeTab::class,
            ArtistCoverSmallTab::class,
            ArtistAnidbResourceTab::class,
            ArtistAnilistResourceTab::class,
            ArtistAnimePlanetResourceTab::class,
            ArtistAnnResourceTab::class,
            ArtistMalResourceTab::class,
            ArtistOfficialSiteResourceTab::class,
            ArtistSpotifyResourceTab::class,
            ArtistXResourceTab::class,
            ArtistYoutubeResourceTab::class,
            ArtistSongTab::class,
        ]);
    }
}
