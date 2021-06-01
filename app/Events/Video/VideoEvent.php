<?php

declare(strict_types=1);

namespace App\Events\Video;

use App\Models\Video;

/**
 * Class VideoEvent.
 */
abstract class VideoEvent
{
    /**
     * The video that has fired this event.
     *
     * @var Video
     */
    protected Video $video;

    /**
     * Create a new event instance.
     *
     * @param Video $video
     * @return void
     */
    public function __construct(Video $video)
    {
        $this->video = $video;
    }

    /**
     * Get the video that has fired this event.
     *
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }
}
