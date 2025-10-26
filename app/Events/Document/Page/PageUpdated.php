<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Events\Base\Wiki\WikiUpdatedEvent;
use App\Models\Document\Page;

/**
 * @extends WikiUpdatedEvent<Page>
 */
class PageUpdated extends WikiUpdatedEvent
{
    public function __construct(Page $page)
    {
        parent::__construct($page);
        $this->initializeEmbedFields($page);
    }

    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been updated.";
    }
}
