<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;

class AnimeThemesTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'anime-theme-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.anime.themes.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_THEMES);
    }
}
