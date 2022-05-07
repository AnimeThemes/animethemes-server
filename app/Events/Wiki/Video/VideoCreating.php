<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\BaseEvent;
use App\Models\Wiki\Video;

/**
 * Class VideoCreating.
 *
 * @extends BaseEvent<Video>
 */
class VideoCreating extends BaseEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Video  $video
     */
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Video
     */
    public function getModel(): Video
    {
        return $this->model;
    }
}
