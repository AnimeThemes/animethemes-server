<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Artist;
use Illuminate\Database\Eloquent\Builder;

class ArtistSongStaffTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'artist-song-staff-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.artist.song_staff.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave(Artist::RELATION_SONG_STAFF)
            ->whereDoesntHave(Artist::RELATION_MEMBER_SONG_STAFF);
    }

    public function getBadge(): ?string
    {
        return (string) Artist::query()
            ->whereDoesntHave(Artist::RELATION_SONG_STAFF)
            ->whereDoesntHave(Artist::RELATION_MEMBER_SONG_STAFF)
            ->count();
    }
}
