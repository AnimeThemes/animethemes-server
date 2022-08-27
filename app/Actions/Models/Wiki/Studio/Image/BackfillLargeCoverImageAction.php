<?php

declare(strict_types=1);

namespace App\Actions\Models\Wiki\Studio\Image;

use App\Actions\Models\Wiki\Studio\BackfillStudioImageAction;
use App\Enums\Models\Wiki\ImageFacet;
use App\Enums\Models\Wiki\ResourceSite;
use App\Models\Wiki\ExternalResource;
use App\Models\Wiki\Image;
use Illuminate\Http\Client\RequestException;

/**
 * Class BackfillLargeCoverImageAction.
 */
class BackfillLargeCoverImageAction extends BackfillStudioImageAction
{
    /**
     * Get the facet to backfill.
     *
     * @return ImageFacet
     */
    protected function getFacet(): ImageFacet
    {
        return ImageFacet::COVER_LARGE();
    }

    /**
     * Query third-party APIs to find Image.
     *
     * @return Image|null
     *
     * @throws RequestException
     */
    protected function getImage(): ?Image
    {
        $malResource = $this->getModel()->resources()->firstWhere(ExternalResource::ATTRIBUTE_SITE, ResourceSite::MAL);
        if ($malResource instanceof ExternalResource) {
            return $this->getMalImage($malResource);
        }

        return null;
    }

    /**
     * Query MAL API for large cover image.
     *
     * @param  ExternalResource  $malResource
     * @return Image|null
     *
     * @throws RequestException
     */
    protected function getMalImage(ExternalResource $malResource): ?Image
    {
        return $this->createImage("https://cdn.myanimelist.net/img/common/companies/$malResource->external_id.png");
    }
}
