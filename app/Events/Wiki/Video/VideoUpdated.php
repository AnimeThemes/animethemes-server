<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Video;

/**
 * Class VideoUpdated.
 *
 * @extends WikiUpdatedEvent<Video>
 */
class VideoUpdated extends WikiUpdatedEvent
{
    public function __construct(Video $video)
    {
        parent::__construct($video);
        $this->initializeEmbedFields($video);
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
        return "Video '**{$this->getModel()->getName()}**' has been updated.";
    }
}
