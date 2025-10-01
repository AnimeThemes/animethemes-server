<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Theme\Entry;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Builder;

class AnimeThemeEntryVideoTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'entry-videos-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.anime.theme.entry.video.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(AnimeThemeEntry::RELATION_VIDEOS);
    }
}
