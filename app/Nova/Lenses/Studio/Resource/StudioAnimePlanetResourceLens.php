<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Studio\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Nova\Lenses\Studio\StudioResourceLens;

/**
 * Class StudioAnimePlanetResourceLens.
 */
class StudioAnimePlanetResourceLens extends StudioResourceLens
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
        return 'studio-anime-planet-resource-lens';
    }
}
