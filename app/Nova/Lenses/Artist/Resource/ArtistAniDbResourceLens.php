<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Artist\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Nova\Lenses\Artist\ArtistResourceLens;

/**
 * Class ArtistAniDbResourceLens.
 */
class ArtistAniDbResourceLens extends ArtistResourceLens
{
    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIDB();
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
        return 'artist-anidb-resource-lens';
    }
}
