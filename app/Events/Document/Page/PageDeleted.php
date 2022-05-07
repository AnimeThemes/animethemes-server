<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Wiki\WikiDeletedEvent;
use App\Models\Document\Page;
use App\Nova\Resources\Document\Page as PageResource;

/**
 * Class PageDeleted.
 *
 * @extends WikiDeletedEvent<Page>
 */
class PageDeleted extends WikiDeletedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Page  $page
     */
    public function __construct(Page $page)
    {
        parent::__construct($page);
    }

    /**
     * Get the model that has fired this event.
     *
     * @return Page
     */
    public function getModel(): Page
    {
        return $this->model;
    }

    /**
     * Get the description for the Discord message payload.
     *
     * @return string
     */
    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been deleted.";
    }

    /**
     * Get the message for the nova notification.
     *
     * @return string
     */
    protected function getNotificationMessage(): string
    {
        return "Page '{$this->getModel()->getName()}' has been deleted. It will be automatically pruned in one week. Please review.";
    }

    /**
     * Get the URL for the nova notification.
     *
     * @return string
     */
    protected function getNotificationUrl(): string
    {
        $uriKey = PageResource::uriKey();

        return "/resources/$uriKey/{$this->getModel()->getKey()}";
    }
}
