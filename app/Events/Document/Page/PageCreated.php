<?php

declare(strict_types=1);

namespace App\Events\Document\Page;

use App\Actions\Models\Document\UpdatePageRelations;
use App\Contracts\Events\UpdateRelationsEvent;
use App\Events\Base\Wiki\WikiCreatedEvent;
use App\Models\Document\Page;

/**
 * @extends WikiCreatedEvent<Page>
 */
class PageCreated extends WikiCreatedEvent implements UpdateRelationsEvent
{
    protected function getDiscordMessageDescription(): string
    {
        return "Page '**{$this->getModel()->getName()}**' has been created.";
    }

    public function updateRelations(): void
    {
        new UpdatePageRelations()->handle($this->getModel());
    }
}
