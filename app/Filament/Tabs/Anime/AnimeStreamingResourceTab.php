<?php

declare(strict_types=1);

namespace App\Filament\Tabs\Anime;

use App\Enums\Models\Wiki\ResourceSite;
use App\Filament\Tabs\BaseTab;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Builder;

class AnimeStreamingResourceTab extends BaseTab
{
    public static function getSlug(): string
    {
        return 'anime-streaming-resource-tab';
    }

    /**
     * @return ResourceSite[]
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

    public function getLabel(): string
    {
        return __('filament.tabs.anime.streaming_resources.name');
    }

    public function modifyQuery(Builder $query): Builder
    {
        return $query->whereDoesntHave(Anime::RELATION_RESOURCES, function (Builder $resourceQuery): void {
            $resourceQuery->where(function (Builder $query): void {
                foreach (static::sites() as $site) {
                    $query->orWhere(ExternalResource::ATTRIBUTE_SITE, $site->value);
                }
            });
        });
    }
}
