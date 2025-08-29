<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Video;

/**
 * @extends WikiCreatedEvent<Video>
 */
class VideoCreated extends WikiCreatedEvent
{
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Video
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Video '**{$this->getModel()->getName()}**' has been created.";
    }
}
