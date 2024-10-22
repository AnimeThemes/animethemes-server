<?php

declare(strict_types=1);

namespace App\Actions\Models\List;

use App\Enums\Models\List\ExternalProfileSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Class BaseStoreExternalProfileAction.
 */
abstract class BaseStoreExternalProfileAction
{
    protected Collection $resources;

    /**
     * Preload the resources for performance proposals.
     *
     * @param  ExternalProfileSite  $profileSite
     * @param  array  $entries
     * @return void
     */
    protected function preloadResources(ExternalProfileSite $profileSite, array $entries): void
    {
        $this->resources = Cache::flexible("externalprofile_resources", [60, 300], function () use ($profileSite, $entries) {
            return ExternalResource::query()
                ->where(ExternalResource::ATTRIBUTE_SITE, $profileSite->getResourceSite()->value)
                ->whereIn(ExternalResource::ATTRIBUTE_EXTERNAL_ID, Arr::pluck($entries, 'external_id'))
                ->with(ExternalResource::RELATION_ANIME)
                ->get()
                ->mapWithKeys(fn (ExternalResource $resource) => [$resource->external_id => $resource->anime]);
        });
    }

    /**
     * Get the animes by the external id.
     *
     * @param  int  $externalId
     * @return Collection<int, Anime>
     */
    protected function getAnimesByExternalId(int $externalId): Collection
    {
        return $this->resources[$externalId] ?? new Collection();
    }
}