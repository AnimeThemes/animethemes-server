<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction;
use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class VideoScriptForceDeleting.
 *
 * @extends BaseEvent<VideoScript>
 */
class VideoScriptForceDeleting extends BaseEvent implements RemoveFromStorageEvent
{
    /**
     * Create a new event instance.
     *
     * @param  VideoScript  $script
     */
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return VideoScript
     */
    public function getModel(): VideoScript
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
        $action = new DeleteScriptAction($this->getModel());

        $action->handle();
    }
}
