<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Anime\ExternalApi;

use App\Actions\Models\Wiki\ExternalApiAction;
use App\Contracts\Actions\Models\Wiki\BackfillStudios;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\Anime;
use App\Models\Wiki\ExternalResource;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class MalAnimeExternalApiAction extends ExternalApiAction implements BackfillStudios
{
    /**
     * Get the site to backfill.
     */
    public function getSite(): ResourceSite
    {
        return ResourceSite::MAL;
    }

    /**
     * Set the response after the request.
     *
     * @param  BelongsToMany<ExternalResource, Anime>  $resources
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
     * Get the mapped studios.
     *
     * @return array
     */
    public function getStudios(): array
    {
        return Arr::get($this->response, 'studios', []);
    }
}
