<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class SongArtistTab.
 */
class SongArtistTab extends BaseTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getKey(): string
    {
        return 'song-artist-tab';
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
        return __('filament.tabs.song.artist.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Song::RELATION_ARTISTS);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Song::query()->whereDoesntHave(Song::RELATION_ARTISTS)->count();
    }
}
