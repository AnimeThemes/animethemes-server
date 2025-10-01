<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Song;
use Illuminate\Database\Eloquent\Builder;

class SongPerformanceTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'song-performance-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.song.performance.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Song::RELATION_PERFORMANCES);
    }

    public function getBadge(): int
    {
        return Song::query()->whereDoesntHave(Song::RELATION_PERFORMANCES)->count();
    }
}
