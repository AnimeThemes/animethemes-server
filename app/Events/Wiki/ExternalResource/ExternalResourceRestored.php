<?php

declare(strict_types=1);

namespace App\Events\Wiki\ExternalResource;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Wiki\ExternalResource;

/**
 * @extends WikiRestoredEvent<ExternalResource>
 */
class ExternalResourceRestored extends WikiRestoredEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Resource '**{$this->getModel()->getName()}**' has been restored.";
    }
}
