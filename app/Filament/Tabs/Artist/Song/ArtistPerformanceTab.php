<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Artist\Song;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Artist;
use Illuminate\Database\Eloquent\Builder;

class ArtistPerformanceTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'artist-performances-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.artist.performance.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave(Artist::RELATION_PERFORMANCES)
            ->whereDoesntHave(Artist::RELATION_MEMBERSHIPS_PERFORMANCES)
            ->whereDoesntHave(Artist::RELATION_GROUPSHIPS_PERFORMANCES);
    }

    public function getBadge(): int
    {
        return Artist::query()
            ->whereDoesntHave(Artist::RELATION_PERFORMANCES)
            ->whereDoesntHave(Artist::RELATION_MEMBERSHIPS_PERFORMANCES)
            ->whereDoesntHave(Artist::RELATION_GROUPSHIPS_PERFORMANCES)
            ->count();
    }
}
