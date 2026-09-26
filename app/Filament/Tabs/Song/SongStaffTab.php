<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Builder;

class SongStaffTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'song-staff-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.song.song_staff.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Song::RELATION_STAFF);
    }

    public function getBadge(): ?string
    {
        return (string) Song::query()->whereDoesntHave(Song::RELATION_STAFF)->count();
    }
}
