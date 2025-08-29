<?php

declare(strict_types=1);

namespace App\Events\Wiki\Video\Script;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Video\VideoScript;

/**
 * @extends WikiRestoredEvent<VideoScript>
 */
class VideoScriptRestored extends WikiRestoredEvent
{
    public function __construct(VideoScript $script)
    {
        parent::__construct($script);
    }

    public function getModel(): VideoScript
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been restored.";
    }
}
