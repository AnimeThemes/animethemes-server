<?php

declare(strict_types=1);

namespace App\Events\Wiki\Image;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Wiki\Image;

/**
 * @extends WikiCreatedEvent<Image>
 */
class ImageCreated extends WikiCreatedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Image '**{$this->getModel()->getName()}**' has been created.";
    }
}
