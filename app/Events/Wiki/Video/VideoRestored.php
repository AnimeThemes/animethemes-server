<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Video;

/**
 * Class VideoRestored.
 *
 * @extends WikiRestoredEvent<Video>
 */
class VideoRestored extends WikiRestoredEvent
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

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Video '**{$this->getModel()->getName()}**' has been restored.";
    }
}
