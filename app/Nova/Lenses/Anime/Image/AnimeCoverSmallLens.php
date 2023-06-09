<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime\Image;

use App\Enums\Models\Wiki\ImageFacet;
use App\Nova\Lenses\Anime\AnimeImageLens;

/**
 * Class AnimeCoverSmallLens.
 */
class AnimeCoverSmallLens extends AnimeImageLens
{
    /**
     * The image facet.
     *
     * @return ImageFacet
     */
    protected static function facet(): ImageFacet
    {
        return ImageFacet::COVER_SMALL;
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
        return 'anime-cover-small-lens';
    }
}
