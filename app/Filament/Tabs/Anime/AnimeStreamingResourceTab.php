<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class AnimeStreamingResourceTab.
 */
class AnimeStreamingResourceTab extends BaseTab
{
    /**
     * Get the key for the tab.
     *
     * @return string
     */
    public static function getKey(): string
    {
        return 'anime-streaming-resource-tab';
    }

    /**
     * The resources site.
     *
     * @return array<int, ResourceSite>
     */
    protected static function sites(): array
    {
        return [
            ResourceSite::CRUNCHYROLL,
            ResourceSite::HIDIVE,
            ResourceSite::NETFLIX,
            ResourceSite::DISNEY_PLUS,
            ResourceSite::HULU,
            ResourceSite::AMAZON_PRIME_VIDEO,
        ];
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
        return __('filament.tabs.anime.streaming_resources.name');
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
            $resourceQuery->where(function (Builder $query) {
                foreach (static::sites() as $site) {
                    $query->orWhere(ExternalResource::ATTRIBUTE_SITE, $site->value);
                }
            });
        });
    }
}
