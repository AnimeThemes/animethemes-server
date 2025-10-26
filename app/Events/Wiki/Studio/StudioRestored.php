<?php

declare(strict_types=1);

namespace App\Events\Wiki\Studio;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Studio;

/**
 * @extends WikiRestoredEvent<Studio>
 */
class StudioRestored extends WikiRestoredEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Studio '**{$this->getModel()->getName()}**' has been restored.";
    }
}
