<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Audio;

/**
 * @extends WikiRestoredEvent<Audio>
 */
class AudioRestored extends WikiRestoredEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been restored.";
    }
}
