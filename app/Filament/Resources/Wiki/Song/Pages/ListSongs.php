<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\SongResource;
use App\Filament\Tabs\Song\Resource\SongAmazonMusicResourceTab;
use App\Filament\Tabs\Song\Resource\SongAnidbResourceTab;
use App\Filament\Tabs\Song\Resource\SongAppleMusicResourceTab;
use App\Filament\Tabs\Song\Resource\SongSpotifyResourceTab;
use App\Filament\Tabs\Song\Resource\SongYoutubeMusicResourceTab;
use App\Filament\Tabs\Song\Resource\SongYoutubeResourceTab;
use App\Filament\Tabs\Song\SongPerformanceTab;
use App\Models\Wiki\Song;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListSongs extends BaseListResources
{
    use HasTabs;

    protected static string $resource = SongResource::class;

    /**
     * Using Laravel Scout to search.
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, Song::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return ['all' => Tab::make()] + $this->toArray([
            SongAmazonMusicResourceTab::class,
            SongAnidbResourceTab::class,
            SongAppleMusicResourceTab::class,
            SongSpotifyResourceTab::class,
            SongYoutubeMusicResourceTab::class,
            SongYoutubeResourceTab::class,
            SongPerformanceTab::class,
        ]);
    }
}
