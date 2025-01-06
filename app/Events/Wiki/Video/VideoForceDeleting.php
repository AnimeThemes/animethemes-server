<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video;

use App\Actions\Storage\Wiki\Video\DeleteVideoAction;
use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Video;

/**
 * Class VideoForceDeleting.
 *
 * @extends BaseEvent<Video>
 */
class VideoForceDeleting extends BaseEvent implements RemoveFromStorageEvent
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
     * Remove the image from the bucket.
     *
     * @return void
     */
    public function removeFromStorage(): void
    {
        $action = new DeleteVideoAction($this->getModel());

        $action->handle();
    }
}
