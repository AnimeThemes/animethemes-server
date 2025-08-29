<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Filament\Resources\Document\Page as PageFilament;
use App\Models\Document\Page;

/**
 * @extends WikiDeletedEvent<Page>
 */
class PageDeleted extends WikiDeletedEvent
{
    public function __construct(Page $page)
    {
        parent::__construct($page);
    }

    /**
     * Get the model that has fired this event.
     */
    public function getModel(): Page
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the filament notification.
     */
    protected function getNotificationMessage(): string
    {
        return "Page '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the Filament notification.
     */
    protected function getFilamentNotificationUrl(): string
    {
        return PageFilament::getUrl('view', ['record' => $this->getModel()]);
    }
}
