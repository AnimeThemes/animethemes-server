<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Studio;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeStudioTab.
 */
class AnimeStudioTab extends BaseTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'anime-studio-tab';
    }

    /**
     * Get the displayable name of the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getLabel(): string
    {
        return __('filament.tabs.anime.studios.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_STUDIOS);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Anime::query()->whereDoesntHave(Anime::RELATION_STUDIOS)->count();
    }
}
