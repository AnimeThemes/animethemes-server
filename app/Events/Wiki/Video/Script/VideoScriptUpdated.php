<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Wiki\Video\VideoScript;

/**
 * @extends WikiUpdatedEvent<VideoScript>
 */
class VideoScriptUpdated extends WikiUpdatedEvent
{
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
        $this->initializeEmbedFields($script);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been updated.";
    }
}
