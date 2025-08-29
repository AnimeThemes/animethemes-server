<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Video\VideoScript;

/**
 * @extends WikiCreatedEvent<VideoScript>
 */
class VideoScriptCreated extends WikiCreatedEvent
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
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been created.";
    }
}
