<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Artist\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Nova\Lenses\Artist\ArtistImageLens;

/**
 * Class ArtistCoverLargeLens.
 */
class ArtistCoverLargeLens extends ArtistImageLens
{
    /**
     * The image facet.
     *
     * @return ImageFacet
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::COVER_LARGE;
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     *
     * @noinspection PhpMissingParentCallCommonInspection
     */
    public function uriKey(): string
    {
        return 'artist-cover-large-lens';
    }
}
