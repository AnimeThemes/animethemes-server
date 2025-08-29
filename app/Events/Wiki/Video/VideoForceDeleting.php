<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Video;

/**
 * @extends BaseEvent<Video>
 */
class VideoForceDeleting extends BaseEvent implements RemoveFromStorageEvent
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
     * Remove the video from the buckets.
     */
    public function removeFromStorage(): void
    {
        $action = new DeleteVideoAction($this->getModel());

        $action->handle();
    }
}
