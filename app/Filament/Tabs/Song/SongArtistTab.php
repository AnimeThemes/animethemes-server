<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Builder;

class SongArtistTab extends BaseTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'song-artist-tab';
    }

    /**
     * Get the displayable name of the tab.
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
     */
    public function getBadge(): int
    {
        return Song::query()->whereDoesntHave(Song::RELATION_ARTISTS)->count();
    }
}
