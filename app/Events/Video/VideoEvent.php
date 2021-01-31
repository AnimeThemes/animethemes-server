<?php

namespace App\Events\Video;

use App\Models\Video;

abstract class VideoEvent
{
    /**
     * The video that has fired this event.
     *
     * @var \App\Models\Video
     */
    protected $video;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Video $video
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Get the video that has fired this event.
     *
     * @return \App\Models\Video
     */
    public function getVideo()
    {
        return $this->video;
    }
}
