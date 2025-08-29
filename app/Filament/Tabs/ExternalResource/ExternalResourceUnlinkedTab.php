<?php

declare(strict_types=1);

namespace App\Filament\Tabs\ExternalResource;

use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

class ExternalResourceUnlinkedTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'external-resource-unlinked-tab';
    }

    public function getLabel(): string
    {
        return __('filament.tabs.external_resource.unlinked.name');
    }

    /**
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave(ExternalResource::RELATION_ANIME)
            ->whereDoesntHave(ExternalResource::RELATION_ANIMETHEMEENTRIES)
            ->whereDoesntHave(ExternalResource::RELATION_ARTISTS)
            ->whereDoesntHave(ExternalResource::RELATION_SONGS)
            ->whereDoesntHave(ExternalResource::RELATION_STUDIOS);
    }

    public function getBadge(): int
    {
        return ExternalResource::query()
            ->whereDoesntHave(ExternalResource::RELATION_ANIME)
            ->whereDoesntHave(ExternalResource::RELATION_ANIMETHEMEENTRIES)
            ->whereDoesntHave(ExternalResource::RELATION_ARTISTS)
            ->whereDoesntHave(ExternalResource::RELATION_SONGS)
            ->whereDoesntHave(ExternalResource::RELATION_STUDIOS)
            ->count();
    }
}
