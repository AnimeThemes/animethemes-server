<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Studio;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class StudioUnlinkedTab.
 */
class StudioUnlinkedTab extends BaseTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public static function getKey(): string
    {
        return 'studio-unlinked-tab';
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
        return __('filament.tabs.studio.unlinked.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Studio::RELATION_ANIME);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return Studio::query()->whereDoesntHave(Studio::RELATION_ANIME)->count();
    }
}
