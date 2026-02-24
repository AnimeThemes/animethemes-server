<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Document\DocumentRestoredEvent;
use App\Models\Document\Page;

/**
 * @extends DocumentRestoredEvent<Page>
 */
class PageRestored extends DocumentRestoredEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been restored.";
    }
}
