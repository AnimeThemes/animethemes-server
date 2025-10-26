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
    protected function getDiscordMessageDescription(): string
    {
        return "Script '**{$this->getModel()->getName()}**' has been created.";
    }
}
