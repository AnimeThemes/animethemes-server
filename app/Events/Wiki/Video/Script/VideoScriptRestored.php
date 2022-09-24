<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class VideoScriptRestored.
 *
 * @extends WikiRestoredEvent<VideoScript>
 */
class VideoScriptRestored extends WikiRestoredEvent
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
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been restored.";
    }
}
