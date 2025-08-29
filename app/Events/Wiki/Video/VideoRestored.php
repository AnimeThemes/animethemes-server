<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Video;

/**
 * @extends WikiRestoredEvent<Video>
 */
class VideoRestored extends WikiRestoredEvent
{
    public function __construct(Video $video)
    {
        parent::__construct($video);
    }

    public function getModel(): Video
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Video '**{$this->getModel()->getName()}**' has been restored.";
    }
}
