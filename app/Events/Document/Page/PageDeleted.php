<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Document\DocumentDeletedEvent;
use App\Filament\Resources\Document\PageResource as PageFilament;
use App\Models\Document\Page;

/**
 * @extends DocumentDeletedEvent<Page>
 */
class PageDeleted extends DocumentDeletedEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been deleted.";
    }

    protected function getNotificationMessage(): string
    {
        return "Page '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    protected function getFilamentNotificationUrl(): string
    {
        return PageFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
