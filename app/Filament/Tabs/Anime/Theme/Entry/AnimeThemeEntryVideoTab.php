<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime\Theme\Entry;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use Illuminate\Database\Eloquent\Builder;

class AnimeThemeEntryVideoTab extends BaseTab
{
    /**
     * Get the slug for the tab.
     *
     * @return string
     */
    public static function getSlug(): string
    {
        return 'entry-videos-tab';
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
        return __('filament.tabs.anime.theme.entry.video.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(AnimeThemeEntry::RELATION_VIDEOS);
    }
}
