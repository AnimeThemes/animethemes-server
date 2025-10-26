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
    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been restored.";
    }
}
