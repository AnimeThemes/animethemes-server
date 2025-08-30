<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Document\Page;

/**
 * @extends WikiCreatedEvent<Page>
 */
class PageCreated extends WikiCreatedEvent
{
    public function __construct(Page $page)
    {
        parent::__construct($page);
    }

    public function getModel(): Page
    {
        return $this->model;
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been created.";
    }
}
