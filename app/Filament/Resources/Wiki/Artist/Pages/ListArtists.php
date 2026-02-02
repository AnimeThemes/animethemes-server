<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Artist\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\ArtistResource;
use App\Filament\Tabs\Artist\Image\ArtistLargeCoverTab;
use App\Filament\Tabs\Artist\Image\ArtistSmallCoverTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnidbResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnilistResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnimePlanetResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistAnnResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistMalResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistOfficialSiteResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistSpotifyResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistXResourceTab;
use App\Filament\Tabs\Artist\Resource\ArtistYoutubeResourceTab;
use App\Filament\Tabs\Artist\Song\ArtistPerformanceTab;
use App\Models\Wiki\Artist;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListArtists extends BaseListResources
{
    use HasTabs;

    protected static string $resource = ArtistResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Artist::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            ArtistLargeCoverTab::class,
            ArtistSmallCoverTab::class,
            ArtistAnidbResourceTab::class,
            ArtistAnilistResourceTab::class,
            ArtistAnimePlanetResourceTab::class,
            ArtistAnnResourceTab::class,
            ArtistMalResourceTab::class,
            ArtistOfficialSiteResourceTab::class,
            ArtistSpotifyResourceTab::class,
            ArtistXResourceTab::class,
            ArtistYoutubeResourceTab::class,
            ArtistPerformanceTab::class,
        ]);
    }
}
