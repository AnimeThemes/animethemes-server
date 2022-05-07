<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Document\Page;

/**
 * Class PageUpdated.
 *
 * @extends WikiUpdatedEvent<Page>
 */
class PageUpdated extends WikiUpdatedEvent
{
    /**
     * Create a new event instance.
     *
     * @param  Page  $page
     */
    public function __construct(Page $page)
    {
        parent::__construct($page);
        $this->initializeEmbedFields($page);
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
        return "Page '**{$this->getModel()->getName()}**' has been updated.";
    }
}
