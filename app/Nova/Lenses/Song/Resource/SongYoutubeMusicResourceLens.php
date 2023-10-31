<?php

declare(strict_types=1);

namespace App\Nova\Lenses\Song\Resource;

use App\Enums\Models\Wiki\ResourceSite;
use App\Nova\Lenses\Song\SongResourceLens;

/**
 * Class SongYoutubeMusicResourceLens.
 */
class SongYoutubeMusicResourceLens extends SongResourceLens
{
    /**
     * The resource site.
     *
     * @return ResourceSite
     */
    protected static function site(): ResourceSite
    {
        return ResourceSite::YOUTUBE_MUSIC;
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
        return 'song-youtube-music-resource-lens';
    }
}
