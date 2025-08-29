<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Actions\Storage\Wiki\Video\Script\DeleteScriptAction;
use App\Contracts\Events\RemoveFromStorageEvent;
use App\Events\BaseEvent;
use App\Models\Wiki\Video\VideoScript;

/**
 * @extends BaseEvent<VideoScript>
 */
class VideoScriptForceDeleting extends BaseEvent implements RemoveFromStorageEvent
{
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): VideoScript
    {
        return $this->model;
    }

    /**
     * Remove the image from the bucket.
     */
    public function removeFromStorage(): void
    {
        $action = new DeleteScriptAction($this->getModel());

        $action->handle();
    }
}
