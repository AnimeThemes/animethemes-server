<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Studio;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;

class AnimeStudioTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'anime-studio-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.anime.studios.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_STUDIOS);
    }

    public function getBadge(): int
    {
        return Anime::query()->whereDoesntHave(Anime::RELATION_STUDIOS)->count();
    }
}
