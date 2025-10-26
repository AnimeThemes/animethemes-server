<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\Image;

/**
 * @extends WikiRestoredEvent<Image>
 */
class ImageRestored extends WikiRestoredEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Image '**{$this->getModel()->getName()}**' has been restored.";
    }
}
