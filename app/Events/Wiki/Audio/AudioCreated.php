<?php

declare(strict_types=1);

namespace App\Events\Wiki\Audio;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Audio;

/**
 * @extends WikiCreatedEvent<Audio>
 */
class AudioCreated extends WikiCreatedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Audio '**{$this->getModel()->getName()}**' has been created.";
    }
}
