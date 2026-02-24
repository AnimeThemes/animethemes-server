<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Actions\Models\Document\UpdatePageRelations;
use App\Contracts\Events\UpdateRelationsEvent;
use App\Events\Base\Document\DocumentUpdatedEvent;
use App\Models\Document\Page;

/**
 * @extends DocumentUpdatedEvent<Page>
 */
class PageUpdated extends DocumentUpdatedEvent implements UpdateRelationsEvent
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

    public function updateRelations(): void
    {
        new UpdatePageRelations()->handle($this->getModel());
    }
}
