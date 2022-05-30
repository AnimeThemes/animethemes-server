<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Models\Wiki\Video;
use App\Nova\Resources\Wiki\Video as VideoResource;

/**
 * Class VideoDeleted.
 *
 * @extends WikiDeletedEvent<Video>
 */
class VideoDeleted extends WikiDeletedEvent
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
        return "Video '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Video '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNotificationUrl(): string
    {
        $uriKey = VideoResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}
