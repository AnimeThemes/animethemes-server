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
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Class ListSongs.
 */
class ListSongs extends BaseListResources
{
    use HasTabs;

    protected static string $resource = Song::class;

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
            $query->whereIn(SongModel::ATTRIBUTE_ID, SongModel::search($search)->take(25)->keys());
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
