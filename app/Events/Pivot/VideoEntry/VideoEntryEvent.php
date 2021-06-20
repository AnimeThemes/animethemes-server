<?php

declare(strict_types=1);

namespace App\Events\Pivot\VideoEntry;

use App\Models\Wiki\Entry;
use App\Models\Wiki\Video;
use App\Pivots\VideoEntry;

/**
 * Class VideoEntryEvent.
 */
abstract class VideoEntryEvent
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
     * @var Entry
     */
    protected Entry $entry;

    /**
     * Create a new event instance.
     *
     * @param VideoEntry $videoEntry
     * @return void
     */
    public function __construct(VideoEntry $videoEntry)
    {
        $this->video = $videoEntry->video;
        $this->entry = $videoEntry->entry;
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
     * @return Entry
     */
    public function getEntry(): Entry
    {
        return $this->entry;
    }
}
