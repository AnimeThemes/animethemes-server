<?php

namespace App\Events\Pivot\VideoEntry;

use App\Pivots\VideoEntry;

abstract class VideoEntryEvent
{
    /**
     * The video that this video entry belongs to.
     *
     * @var \App\Models\Video
     */
    protected $video;

    /**
     * The entry that has fired this event.
     *
     * @var \App\Models\Entry
     */
    protected $entry;

    /**
     * Create a new event instance.
     *
     * @param @var \App\Pivots\VideoEntry $videoEntry
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
     * @return \App\Models\Video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * Get the video that this video entry belongs to.
     *
     * @return \App\Models\Entry
     */
    public function getEntry()
    {
        return $this->entry;
    }
}
