<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\ApiAction;

use App\Actions\Models\Wiki\ApiAction;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

/**
 * Class MalAnimeApiAction.
 */
class MalAnimeApiAction extends ApiAction
{
    /**
     * Get the site to backfill.
     *
     * @return ResourceSite
     */
    public function getSite(): ResourceSite
    {
        return ResourceSite::MAL;
    }

    /**
     * Set the response after the request.
     *
     * @param  BelongsToMany<ExternalResource, Anime>  $resources
     * @return static
     */
    public function handle(BelongsToMany $resources): static
    {
        $resource = $resources->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL->value);

        if ($resource instanceof ExternalResource) {
            $response = Http::withHeaders(['X-MAL-CLIENT-ID' => Config::get('services.mal.client_id')])
                ->get("https://api.myanimelist.net/v2/anime/$resource->external_id", [
                    'fields' => 'studios',
                ])
                ->throw()
                ->json();

            $this->response = $response;
        }

        return $this;
    }

    /**
     * Get the mapping for the resources.
     *
     * @return array<int, string>
     */
    protected function getResourcesMapping(): array
    {
        return [];
    }

    /**
     * Get the mapping for the images.
     *
     * @return array<int, string>
     */
    protected function getImagesMapping(): array
    {
        return [];
    }

    /**
     * Get the mapped studios.
     *
     * @return array<int, string>
     */
    public function getStudios(): array
    {
        return Arr::get($this->response, 'studios', []);
    }
}
