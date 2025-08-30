<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Artist;
use Illuminate\Database\Eloquent\Builder;

class ArtistSongTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'artist-song-lens';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.artist.songs.name');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Artist::RELATION_SONGS);
    }

    public function getBadge(): int
    {
        return Artist::query()->whereDoesntHave(Artist::RELATION_SONGS)->count();
    }
}
