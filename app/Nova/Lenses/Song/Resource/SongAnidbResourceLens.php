<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Nova\Lenses\Song\SongResourceLens;

/**
 * Class SongAnidbResourceLens.
 */
class SongAnidbResourceLens extends SongResourceLens
{
    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::ANIDB;
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
        return 'song-anidb-resource-lens';
    }
}
