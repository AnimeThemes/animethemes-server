<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Models\Wiki\Video;

/**
 * Class VideoEvent.
 */
abstract class VideoEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Video  $video
     * @return void
     */
    public function __construct(protected Video $video)
    {
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
