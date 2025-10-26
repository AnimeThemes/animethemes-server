<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Wiki\WikiRestoredEvent;
use App\Models\Document\Page;

/**
 * @extends WikiRestoredEvent<Page>
 */
class PageRestored extends WikiRestoredEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been restored.";
    }
}
