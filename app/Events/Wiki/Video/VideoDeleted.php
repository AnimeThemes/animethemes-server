<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\Video as VideoFilament;
use App\Models\Wiki\Video;

/**
 * @extends WikiDeletedEvent<Video>
 */
class VideoDeleted extends WikiDeletedEvent
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
        return "Video '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Video '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return VideoFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
