<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Studio\ExternalApi;

use App\Actions\Models\Wiki\ExternalApiAction;
use App\Contracts\Actions\Models\Wiki\BackfillImages;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class MalStudioExternalApiAction.
 */
class MalStudioExternalApiAction extends ExternalApiAction implements BackfillImages
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
     * @param  BelongsToMany<ExternalResource, Studio>  $resources
     */
    public function handle(BelongsToMany $resources): static
    {
        $resource = $resources->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL->value);

        if ($resource instanceof ExternalResource) {
            $this->response = [
                'images' => [
                    'large' => "https://cdn.myanimelist.net/images/company/$resource->external_id.png",
                ],
            ];
        }

        return $this;
    }

    /**
     * Get the mapping for the images.
     *
     * @return string[]
     */
    public function getImagesMapping(): array
    {
        return [
            ImageFacet::LARGE_COVER->value => 'images.large',
        ];
    }
}
