<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Anime\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Nova\Lenses\Anime\AnimeResourceLens;

/**
 * Class AnimePlanetResourceLens.
 */
class AnimePlanetResourceLens extends AnimeResourceLens
{
    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIME_PLANET;
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
        return 'anime-planet-resource-lens';
    }
}
