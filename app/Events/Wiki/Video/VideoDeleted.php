<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Wiki\VideoResource as VideoFilament;
use App\Models\Wiki\Video;

/**
 * @extends WikiDeletedEvent<Video>
 */
class VideoDeleted extends WikiDeletedEvent
{
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
