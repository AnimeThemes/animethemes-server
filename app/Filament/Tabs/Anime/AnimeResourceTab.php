<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

abstract class AnimeResourceTab extends BaseTab
{
    /**
     * The resource site.
     */
    abstract protected static function site(): ResourceSite;

    /**
     * Get the displayable name of the tab.
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function getLabel(): string
    {
        return __('filament.tabs.anime.resources.name', ['site' => static::site()->localize()]);
    }

    /**
     * The query used to refine the models for the tab.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_RESOURCES, function (Builder $resourceQuery) {
            $resourceQuery->where(ExternalResource::ATTRIBUTE_SITE, static::site()->value);
        });
    }
}
