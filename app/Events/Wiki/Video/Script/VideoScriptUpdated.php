<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Video\VideoScript;

/**
 * Class VideoScriptUpdated.
 *
 * @extends WikiUpdatedEvent<VideoScript>
 */
class VideoScriptUpdated extends WikiUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  VideoScript  $script
     */
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
        $this->initializeEmbedFields($script);
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
        return "Script '**{$this->getModel()->getName()}**' has been updated.";
    }
}
