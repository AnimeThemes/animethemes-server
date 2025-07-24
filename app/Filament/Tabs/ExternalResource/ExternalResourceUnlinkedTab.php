<?php

declare(strict_types=1);

namespace App\Filament\Tabs\ExternalResource;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

class ExternalResourceUnlinkedTab extends BaseTab
{
    /**
     * Get the slug for the tab.
     */
    public static function getSlug(): string
    {
        return 'external-resource-unlinked-tab';
    }

    /**
     * Get the displayable name of the tab.
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
