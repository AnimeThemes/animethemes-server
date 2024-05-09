<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Artist;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ArtistSongTab.
 */
class ArtistSongTab extends BaseTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'artist-song-lens';
    }

    /**
     * Get the displayable name of the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getLabel(): string
    {
        return __('filament.tabs.artist.songs.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Artist::RELATION_SONGS);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Artist::query()->whereDoesntHave(Artist::RELATION_SONGS)->count();
    }
}
