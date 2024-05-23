<?php

declare(strict_types=1);

namespace App\Filament\Tabs\ExternalResource;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class ExternalResourceUnlinkedTab.
 */
class ExternalResourceUnlinkedTab extends BaseTab
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
        return 'external-resource-unlinked-tab';
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
        return __('filament.tabs.external_resource.unlinked.name');
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(ExternalResource::RELATION_ANIME)
            ->whereDoesntHave(ExternalResource::RELATION_ARTISTS)
            ->whereDoesntHave(ExternalResource::RELATION_SONGS)
            ->whereDoesntHave(ExternalResource::RELATION_STUDIOS);
    }

    /**
     * Get the badge for the tab.
     *
     * @return int
     */
    public function getBadge(): int
    {
        return ExternalResource::query()->whereDoesntHave(ExternalResource::RELATION_ANIME)
            ->whereDoesntHave(ExternalResource::RELATION_ARTISTS)
            ->whereDoesntHave(ExternalResource::RELATION_SONGS)
            ->whereDoesntHave(ExternalResource::RELATION_STUDIOS)
            ->count();
    }
}
