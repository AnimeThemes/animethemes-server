<?php

declare(strict_types=1);

namespace App\Events\Pivot\AnimeThemeEntryVideo;

use App\Models\Wiki\Anime\Theme\AnimeThemeEntry;
use App\Models\Wiki\Video;
use App\Pivots\AnimeThemeEntryVideo;

/**
 * Class AnimeThemeEntryVideoEvent.
 */
abstract class AnimeThemeEntryVideoEvent
{
    /**
     * The video that this video entry belongs to.
     *
     * @var Video
     */
    protected Video $video;

    /**
     * The entry that this video entry belongs to.
     *
     * @var AnimeThemeEntry
     */
    protected AnimeThemeEntry $entry;

    /**
     * Create a new event instance.
     *
     * @param  AnimeThemeEntryVideo  $animeThemeEntryVideo
     * @return void
     */
    public function __construct(AnimeThemeEntryVideo $animeThemeEntryVideo)
    {
        $this->video = $animeThemeEntryVideo->video;
        $this->entry = $animeThemeEntryVideo->animethemeentry;
    }

    /**
     * Get the video that this video entry belongs to.
     *
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }

    /**
     * Get the entry that this video entry belongs to.
     *
     * @return AnimeThemeEntry
     */
    public function getEntry(): AnimeThemeEntry
    {
        return $this->entry;
    }
}
