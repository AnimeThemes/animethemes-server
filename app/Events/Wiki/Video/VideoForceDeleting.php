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
    /**
     * Remove the video from the buckets.
     */
    public function removeFromStorage(): void
    {
        $action = new DeleteVideoAction($this->getModel());

        $action->handle();
    }
}
