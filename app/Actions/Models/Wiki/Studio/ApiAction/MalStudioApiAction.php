<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Studio\ApiAction;

use App\Actions\Models\Wiki\ApiAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Studio;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class MalStudioApiAction.
 */
class MalStudioApiAction extends ApiAction
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
     * @param  BelongsToMany<ExternalResource, Studio>  $resources
     * @return static
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
        return [
            ImageFacet::COVER_LARGE->value => 'images.large',
        ];
    }
}
