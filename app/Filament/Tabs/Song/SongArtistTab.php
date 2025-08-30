<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Builder;

class SongArtistTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'song-artist-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.song.artist.name');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Song::RELATION_ARTISTS);
    }

    public function getBadge(): int
    {
        return Song::query()->whereDoesntHave(Song::RELATION_ARTISTS)->count();
    }
}
