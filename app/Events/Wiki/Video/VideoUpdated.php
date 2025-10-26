<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Video;

/**
 * @extends WikiUpdatedEvent<Video>
 */
class VideoUpdated extends WikiUpdatedEvent
{
    public function __construct(Video $video)
    {
        parent::__construct($video);
        $this->initializeEmbedFields($video);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Video '**{$this->getModel()->getName()}**' has been updated.";
    }
}
