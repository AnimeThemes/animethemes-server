<?php

declare(strict_types=1);

namespace App\Filament\Resources\Wiki\Song\Pages;

use App\Concerns\Filament\HasTabs;
use App\Filament\Resources\Base\BaseListResources;
use App\Filament\Resources\Wiki\Song;
use App\Filament\Tabs\Song\Resource\SongAmazonMusicResourceTab;
use App\Filament\Tabs\Song\Resource\SongAnidbResourceTab;
use App\Filament\Tabs\Song\Resource\SongAppleMusicResourceTab;
use App\Filament\Tabs\Song\Resource\SongSpotifyResourceTab;
use App\Filament\Tabs\Song\Resource\SongYoutubeMusicResourceTab;
use App\Filament\Tabs\Song\Resource\SongYoutubeResourceTab;
use App\Filament\Tabs\Song\SongArtistTab;
use App\Models\Wiki\Song as SongModel;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ListSongs.
 */
class ListSongs extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Song::class;

    /**
     * Using Laravel Scout to search.
     *
     * @param  Builder  $query
     * @return Builder
     */
    protected function applySearchToTableQuery(Builder $query): Builder
    {
        return $this->makeScout($query, SongModel::class);
    }

    /**
     * Get the tabs available.
     *
     * @return array
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
            SongArtistTab::class,
        ]);
    }
}
